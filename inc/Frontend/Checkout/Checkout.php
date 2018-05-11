<?php
namespace AweBooking\Frontend\Checkout;

use WP_Error;
use AweBooking\Constants;
use AweBooking\Model\Booking;
use AweBooking\Gateway\Gateway;
use AweBooking\Gateway\Gateways;
use AweBooking\Gateway\Response as Gateway_Response;
use AweBooking\Gateway\GatewayException;
use AweBooking\Component\Http\Exceptions\ValidationFailedException;
use AweBooking\Reservation\Reservation;
use AweBooking\Support\Fluent;
use Awethemes\WP_Session\WP_Session;
use Awethemes\Http\Request;

class Checkout {
	/**
	 * The gateways manager instance.
	 *
	 * @var \AweBooking\Gateway\Gateways
	 */
	protected $gateways;

	/**
	 * The session instance.
	 *
	 * @var \Awethemes\WP_Session\WP_Session
	 */
	protected $session;

	/**
	 * The reservation instance.
	 *
	 * @var \AweBooking\Reservation\Reservation
	 */
	protected $reservation;

	/**
	 * The controls instance.
	 *
	 * @var \AweBooking\Component\Form\Form_Builder
	 */
	protected $controls;

	/**
	 * Create a new session store instance.
	 *
	 * @param \AweBooking\Gateway\Gateways        $gateways    The Gateways instance.
	 * @param \Awethemes\WP_Session\WP_Session    $session     The WP_Session instance.
	 * @param \AweBooking\Reservation\Reservation $reservation The Reservation instance.
	 */
	public function __construct( Gateways $gateways, WP_Session $session, Reservation $reservation ) {
		$this->gateways    = $gateways;
		$this->session     = $session;
		$this->reservation = $reservation;
	}

	/**
	 * Process the checkout request.
	 *
	 * @param  \Awethemes\Http\Request $request The http request.
	 * @return \Awethemes\Http\Response
	 *
	 * @throws \RuntimeException
	 */
	public function process( Request $request ) {
		abrs_set_time_limit( 0 );
		Constants::define( 'AWEBOOKING_CHECKOUT', true );

		$errors = new WP_Error();
		$data   = $this->get_posted_data( $request );

		do_action( 'awebooking/checkout/processing', $data, $errors );

		// Update session for customer and totals.
		$this->update_session( $data );

		// Validate posted data.
		$this->validate_posted_data( $data, $errors );
		$this->validate_checkout( $data, $errors );

		if ( ! empty( $errors->errors ) ) {
			throw ( new ValidationFailedException )->set_errors( $errors );
		}

		// Process booking.
		$this->process_customer( $data );
		$booking_id = $this->create_booking( $data );

		if ( is_wp_error( $booking_id ) || ! $booking = abrs_get_booking( $booking_id ) ) {
			throw new \RuntimeException( esc_html__( 'Sorry, we cannot serve your reservation request at this moment.', 'awebooking' ) );
		}

		do_action( 'awebooking/checkout/processed', $booking_id, $data );

		// Process with payment.
		if ( ! empty( $data['payment_method'] ) ) {
			return $this->process_payment( $booking, $this->gateways->get( $data['payment_method'] ) );
		}

		return $this->process_without_payment( $booking );
	}

	/**
	 * Process a booking that does require payment.
	 *
	 * @param  \AweBooking\Support\Booking $booking The booking instance.
	 * @param  string                      $gateway The payment gateway.
	 * @return \Awethemes\Http\Response
	 *
	 * @throws GatewayException
	 */
	protected function process_payment( Booking $booking, Gateway $gateway ) {
		// Store the booking ID in session so it can be re-used after payment failure.
		$this->session->put( 'booking_awaiting_payment', $booking->get_id() );

		// Perform process the payment.
		$response = $gateway->process( $booking );

		if ( ! $response instanceof Gateway_Response ) {
			throw new GatewayException( esc_html__( 'Invalid gateway response.', 'awebooking' ) );
		}

		if ( $response->is_redirect() ) {
			return abrs_redirector()->to( $response->get_redirect_url() )->with( 'response', $response );
		}
	}

	/**
	 * Process a booking that doesn't require payment.
	 *
	 * @param  \AweBooking\Support\Booking $booking The booking instance.
	 * @return \Awethemes\Http\Response
	 */
	protected function process_without_payment( Booking $booking ) {
		$booking->update_status( apply_filters( 'awebooking/booking_status_without_payment', 'on-hold' ) );

		$booking->payment_complete();

		// Flush the reservation data.
		$this->reservation->flush();

		return awebooking( 'redirector' )->to();
	}

	/**
	 * Create a booking from trusted data.
	 *
	 * @param  \AweBooking\Support\Fluent $data The posted data.
	 * @return int|WP_Error
	 */
	public function create_booking( $data ) {
		// Give plugins the opportunity to create an booking themselves.
		if ( $booking_id = apply_filters( 'awebooking/checkout/create_booking', null, $this ) ) {
			return $booking_id;
		}

		// If there is an booking pending payment, we can resume it here.
		$awaiting_booking = $this->session->get( 'booking_awaiting_payment' );

		if ( ! empty( $awaiting_booking ) && ( $booking = abrs_get_booking( $awaiting_booking ) ) && in_array( $booking->get_status(), [ 'pending', 'failed' ] ) ) {
			// $booking->remove_booking_items();
			do_action( 'awebooking/checkout/resume_booking', $booking_id );
		} else {
			$booking = new Booking;
		}

		// Fill the booking data.
		$booking->fill([
			'created_via'             => 'checkout',
			'customer_id'             => apply_filters( 'awebooking/checkout/customer_id', get_current_user_id() ),
			'arrival_time'            => $data['arrival_time'],
			'customer_note'           => $data['customer_note'],

			'customer_first_name'     => $data['customer_first_name'],
			'customer_last_name'      => $data['customer_last_name'],
			'customer_address'        => $data['customer_address'],
			'customer_address_2'      => $data['customer_address_2'],
			'customer_city'           => $data['customer_city'],
			'customer_state'          => $data['customer_state'],
			'customer_postal_code'    => $data['customer_postal_code'],
			'customer_country'        => $data['customer_country'],
			'customer_company'        => $data['customer_company'],
			'customer_phone'          => $data['customer_phone'],
			'customer_email'          => $data['customer_email'],

			'language'                => $this->reservation->language,
			'currency'                => $this->reservation->currency,
			'customer_ip_address'     => abrs_http_request()->ip(),
			'customer_user_agent'     => abrs_http_request()->get_user_agent(),
		]);

		// $booking->set_cart_hash( $cart_hash );
		// $booking->set_prices_include_tax( 'yes' === get_option( 'awebooking_prices_include_tax' ) );

		// $booking->set_shipping_total( WC()->cart->get_shipping_total() );
		// $booking->set_discount_total( WC()->cart->get_discount_total() );
		// $booking->set_discount_tax( WC()->cart->get_discount_tax() );
		// $booking->set_cart_tax( WC()->cart->get_cart_contents_tax() + WC()->cart->get_fee_tax() );
		// $booking->set_shipping_tax( WC()->cart->get_shipping_tax() );
		// $booking->set_total( WC()->cart->get_total( 'edit' ) );

		// $this->create_booking_line_items( $booking, WC()->cart );
		// $this->create_booking_fee_lines( $booking, WC()->cart );
		// $this->create_booking_shipping_lines( $booking, WC()->session->get( 'chosen_shipping_methods' ), WC()->shipping->get_packages() );
		// $this->create_booking_tax_lines( $booking, WC()->cart );
		// $this->create_booking_coupon_lines( $booking, WC()->cart );

		do_action( 'awebooking_checkout_create_booking', $booking, $data );

		// Save the booking.
		$booking_id = $booking->save();

		do_action( 'awebooking_checkout_update_booking_meta', $booking_id, $data );

		return $booking->get_id();
	}

	/**
	 * Create a new customer account if needed.
	 *
	 * @param  \AweBooking\Support\Fluent $data The posted data.
	 * @throws Exception
	 */
	protected function process_customer( $data ) {
		// TODO: ...
	}

	/**
	 * Update customer and session data from the posted checkout data.
	 *
	 * @param array $data An array of posted data.
	 */
	protected function update_session( $data ) {
		$this->session->put( 'selected_payment_method', $data['payment_method'] );

		// Update reservation totals.
		// $this->reservation->calculate_totals();
	}

	/**
	 * Validates that the checkout has enough info to proceed.
	 *
	 * @param  \AweBooking\Support\Fluent $data   The posted data.
	 * @param  \WP_Error                  $errors WP_Error instance.
	 */
	protected function validate_checkout( $data, $errors ) {
		if ( empty( $data['terms'] ) && apply_filters( 'awebooking/checkout/show_terms', abrs_get_page_id( 'terms' ) > 0 ) ) {
			$errors->add( 'terms', esc_html__( 'You must accept our Terms &amp; Conditions.', 'awebooking' ) );
		}

		if ( ! empty( $data['payment_method'] ) ) {
			$gateway = $this->gateways->get( $data['payment_method'] );

			if ( is_null( $gateway ) ) {
				$errors->add( 'payment', esc_html__( 'Invalid payment method.', 'awebooking' ) );
			} else {
				$response = $gateway->validate_fields( $data );

				if ( is_wp_error( $response ) ) {
					$errors->add( 'gateway', $response->get_error_messages() );
				}
			}
		}

		do_action( 'awebooking/checkout/after_validation', $data, $errors );
	}

	/**
	 * Validates the posted checkout data based on field properties.
	 *
	 * @param  \AweBooking\Support\Fluent $data   The posted data.
	 * @param  \WP_Error                  $errors WP_Error instance.
	 */
	protected function validate_posted_data( $data, $errors ) {
		$controls = $this->get_controls();

		foreach ( $controls->prop( 'fields' ) as $args ) {
			$key     = $args['id'];
			$control = $controls->get_field( $key );

			if ( $control->prop( 'required' ) && abrs_blank( $data[ $key ] ) ) {
				/* translators: %s Field name */
				$errors->add( 'required-field', sprintf( __( '%s is a required field.', 'awebooking' ), '<strong>' . esc_html( $control->prop( 'name' ) ) . '</strong>' ) );
			}
		}

		do_action( 'awebooking/checkout/validate_posted_data', $data, $errors );
	}

	/**
	 * Get posted data from the checkout form.
	 *
	 * @param  \Awethemes\Http\Request $request The http request.
	 * @return \AweBooking\Support\Fluent
	 */
	public function get_posted_data( $request ) {
		// Get all sanitized of controls data.
		$data = $this->get_controls()->handle( $request );

		$data['terms'] = $request->filled( 'terms' );
		$data['payment_method'] = abrs_clean( $request->get( 'payment_method' ) );

		return apply_filters( 'awebooking/checkout/posted_data', $data, $request );
	}

	/**
	 * Gets the checkout controls.
	 *
	 * @param  string $fieldset to get.
	 * @return array
	 */
	public function get_controls( $fieldset = '' ) {
		if ( is_null( $this->controls ) ) {
			$this->controls = apply_filters( 'awebooking/checkout/controls', new Form_Controls );
			$this->controls->prepare_fields();
		}

		return $this->controls;
	}
}
