<?php

namespace AweBooking\Frontend\Controllers;

use WP_Error;
use WPLibs\Http\Request;
use AweBooking\Model\Booking;
use AweBooking\Gateway\Gateway;
use AweBooking\Gateway\GatewayException;
use AweBooking\Gateway\Gateways;
use AweBooking\Gateway\Response as Gateway_Response;
use AweBooking\Checkout\Url_Generator;
use AweBooking\Support\Fluent;
use AweBooking\Component\Http\Exceptions\RedirectResponseException;

class Payment_Controller {
	/**
	 * The gateways.
	 *
	 * @var \AweBooking\Gateway\Gateways
	 */
	protected $gateways;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Gateway\Gateways $gateways
	 */
	public function __construct( Gateways $gateways ) {
		$this->gateways = $gateways;
	}

	/**
	 * Display the payment form.
	 *
	 * @param string $token
	 * @return string
	 */
	public function show( $token ) {
		$booking = $this->resolveBooking( $token );

		$gateway = $this->resolveGateWay( $booking );

		// The expired date only have when customer make
		// the reservation directly.
		$expired_seconds = $expired_formatted = null;

		if ( $expired = $booking->get_meta( '_schedule_clean_booking' ) ) {
			$now     = abrs_date_time( 'now' );
			$expired = abrs_date_time( $expired );

			$expired_seconds   = $now->diffInSeconds( $expired );
			$expired_formatted = $now->diff( $expired )->format( '%H:%I:%S' );
		}

		return abrs_get_template_content(
			'payment.php',
			compact( 'booking', 'gateway', 'expired_seconds', 'expired_formatted' )
		);
	}

	/**
	 * Process the payment.
	 *
	 * @param Request $request
	 * @param string  $token
	 * @return mixed
	 */
	public function process( Request $request, $token ) {
		if ( ! $booking = abrs_get_booking_by_public_token( $token ) ) {
			return abrs_redirector()->home();
		}

		$previous_url = abrs_route( sprintf( 'payment/%s', $token ) );

		if ( ! wp_verify_nonce( $request->get( '_wpnonce' ), 'awebooking_payment_process' ) ) {
			abrs_add_notice(
				esc_html__( 'We were unable to process your reservation, please try again.', 'awebooking' ),
				'error'
			);

			return abrs_redirector()->back( $previous_url );
		}

		$gateway = $this->resolveGateWay( $booking );

		// Perfrom validate and handle errors.
		$errors = $this->validatePayment( $request, $booking, $gateway );

		if ( $errors->has_errors() ) {
			foreach ( $errors->get_error_messages() as $error_message ) {
				abrs_add_notice( $error_message, 'error' );
			}

			return abrs_redirector()->back( $previous_url )->with_input();
		}

		// Perform process the payment.
		try {
			$response = $this->processPayment( $request, $booking, $gateway );

			if ( $response->is_redirect() ) {
				return abrs_redirector()->to( $response->get_redirect_url() );
			}

			if ( $response->is_successful() ) {
				$url_generator = new Url_Generator( $booking );

				return abrs_redirector()->to( $url_generator->get_booking_received_url() );
			}
		} catch ( \Exception $e ) {
			abrs_add_notice( $e->getMessage(), 'error' );
		}

		return abrs_redirector()->back( $previous_url )->with_input();
	}

	/**
	 * Validates that the payment has enough info to proceed.
	 *
	 * @param \WPLibs\Http\Request        $request
	 * @param \AweBooking\Model\Booking   $booking
	 * @param \AweBooking\Gateway\Gateway $gateway
	 * @return \WP_Error
	 */
	protected function validatePayment( Request $request, Booking $booking, Gateway $gateway ) {
		$data   = new Fluent( abrs_clean( $request->all() ) );
		$errors = new WP_Error();

		$response = $gateway->validate_fields( $data, $request );

		if ( is_wp_error( $response ) ) {
			$errors->add( 'gateway', $response->get_error_message() );
		} elseif ( false === $response ) {
			$errors->add(
				'gateway',
				esc_html__( 'Sorry, there was an error processing your payment. Please try again later.', 'awebooking' )
			);
		}

		do_action( 'abrs_validate_checkout', $errors, $booking, $request );

		return $errors;
	}

	/**
	 * //
	 *
	 * @param Request $request
	 * @param Booking $booking
	 * @param Gateway $gateway
	 * @return \AweBooking\Gateway\Response
	 */
	protected function processPayment( Request $request, Booking $booking, Gateway $gateway ) {
		do_action( 'abrs_payment_processing', $booking, $gateway, $request );

		// Perform process the payment.
		$response = $gateway->process( $booking, $request );

		if ( ! $response instanceof Gateway_Response ) {
			throw new GatewayException( esc_html__( 'Invalid gateway response.', 'awebooking' ) );
		}

		do_action( 'abrs_payment_processed', $response, $booking, $gateway, $request );

		return $response->data( $booking );
	}

	/**
	 * //
	 *
	 * @param string $token
	 * @return \AweBooking\Model\Booking
	 */
	protected function resolveBooking( $token ) {
		if ( ! $booking = abrs_get_booking_by_public_token( $token ) ) {
			throw new RedirectResponseException( home_url() );
		}

		if ( ! in_array( $booking->get_status(), [ 'pending', 'inprocess' ] ) ) {
			throw new \RuntimeException(
				esc_html__( 'Cannot to process this reservation, please try again.', 'awebooking' )
			);
		}

		return $booking;
	}

	/**
	 * //
	 *
	 * @param \AweBooking\Model\Booking $booking
	 * @return mixed|\WPLibs\Http\Redirect_Response|null
	 */
	protected function resolveGateWay( Booking $booking ) {
		$last_payment = abrs_get_last_booking_payment( $booking->get_id() );

		if ( $last_payment && $last_payment->get( 'amount' ) > 0 ) {
			throw new \RuntimeException(
				esc_html__( 'Cannot to process this reservation, please try again.', 'awebooking' )
			);
		}

		$payment_method = $last_payment
			? $last_payment->get( 'method' )
			: $booking->get( 'payment_method' );

		if ( $gateway = $this->gateways->get( $payment_method ) ) {
			return $gateway;
		}

		throw new \RuntimeException(
			esc_html__( 'Something went wrong, please try again!', 'awebooking' )
		);
	}
}
