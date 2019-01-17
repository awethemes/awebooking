<?php

namespace AweBooking\Checkout;

use WP_Error;
use AweBooking\Constants;
use AweBooking\Model\Booking;
use AweBooking\Model\Booking\Fee_Item;
use AweBooking\Model\Booking\Tax_Item;
use AweBooking\Model\Booking\Room_Item;
use AweBooking\Model\Booking\Service_Item;
use AweBooking\Gateway\Gateway;
use AweBooking\Gateway\Gateways;
use AweBooking\Gateway\Response as Gateway_Response;
use AweBooking\Gateway\GatewayException;
use AweBooking\Availability\Room_Rate;
use AweBooking\Reservation\Reservation;
use WPLibs\Form\Helper\Validator;
use AweBooking\Component\Http\Exceptions\ValidationFailedException;
use AweBooking\Support\Fluent;
use WPLibs\Session\WP_Session;
use WPLibs\Http\Request;
use Illuminate\Support\Arr;

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
	 * @var \WPLibs\Session\Store
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
	 * @var \AweBooking\Component\Form\Form
	 */
	protected $controls;

	/**
	 * Create a new session store instance.
	 *
	 * @param \AweBooking\Gateway\Gateways        $gateways    The Gateways instance.
	 * @param \WPLibs\Session\WP_Session          $session     The WP_Session instance.
	 * @param \AweBooking\Reservation\Reservation $reservation The Reservation instance.
	 */
	public function __construct( Gateways $gateways, WP_Session $session, Reservation $reservation ) {
		$this->gateways    = $gateways;
		$this->session     = $session;
		$this->reservation = $reservation;
	}

	/**
	 * Output checkout content.
	 *
	 * @return void
	 */
	public function output() {
		$request = abrs_http_request();

		if ( $request->filled( 'booking-received' ) ) {
			$this->output_received( $request );
			return;
		}

		if ( $this->reservation->is_empty() ) {
			abrs_get_template( 'checkout/empty.php' );
			return;
		}

		// Setup the controls.
		$controls = $this->get_controls();

		abrs_get_template( 'checkout/checkout.php', compact( 'request', 'controls' ) );
	}

	/**
	 * Show the booking received.
	 *
	 * @param \WPLibs\Http\Request $request Current http request.
	 * @return void
	 */
	protected function output_received( $request ) {
		$booking_id = apply_filters( 'abrs_thankyou_booking_id', absint( $request->get( 'booking-received' ) ) );

		$booking = abrs_get_booking( $booking_id );

		if ( ! $booking ) {
			return;
		}

		if ( ! $request->has( 'token' ) || ! hash_equals( (string) $request->token, (string) $booking->get_public_token() ) ) {
			return;
		}

		// Empty the awaiting payment in the session.
		abrs_session()->remove( 'booking_awaiting_payment' );

		// Flush the current reservation.
		$this->reservation->flush();

		abrs_get_template( 'checkout/thankyou.php', compact( 'booking' ) );
	}

	/**
	 * Process the checkout request.
	 *
	 * @param  \WPLibs\Http\Request $request The http request.
	 *
	 * @return \AweBooking\Gateway\Response
	 *
	 * @throws \Exception
	 * @throws \AweBooking\Checkout\RuntimeException
	 */
	public function process( Request $request ) {
		abrs_set_time_limit( 0 );
		Constants::define( 'AWEBOOKING_CHECKOUT', true );

		// Resolve the current request.
		if ( $res_request = $this->reservation->get_previous_request() ) {
			$this->reservation->set_current_request( $res_request );
		}

		do_action( 'abrs_prepare_checkout_process', $this, $request );

		$this->reservation->maybe_flush();

		if ( $this->reservation->is_empty() ) {
			throw new RuntimeException( esc_html__( 'Sorry, your session has expired.', 'awebooking' ) );
		}

		$errors = new WP_Error();
		$data   = $this->get_posted_data( $request );

		do_action( 'abrs_checkout_processing', $this, $errors, $data, $request );

		// Update session for customer and totals.
		$this->update_session( $data );

		// Validate posted data.
		$this->validate_posted_data( $errors, $data );
		$this->validate_checkout( $errors, $data, $request );

		if ( ! empty( $errors->errors ) ) {
			throw ( new ValidationFailedException )->set_errors( $errors );
		}

		// Process booking.
		$this->process_customer( $data );

		$booking_id = $this->create_booking( $data );

		if ( ! $booking_id || is_wp_error( $booking_id ) || ! $booking = abrs_get_booking( $booking_id ) ) {
			throw new RuntimeException( esc_html__( 'Sorry, we cannot serve your reservation request at this moment.', 'awebooking' ) );
		}

		// Fire checkout processed action.
		do_action( 'abrs_checkout_processed', $booking->get_id(), $this, $data );

		// Process with payment.
		if ( ! empty( $data['payment_method'] ) ) {
			$gateway = $this->gateways->get( $data['payment_method'] );

			return $this->process_payment( $booking, $gateway, $request );
		}

		return $this->process_without_payment( $booking );
	}

	/**
	 * Update customer and session data from the posted checkout data.
	 *
	 * @param \AweBooking\Support\Fluent $data An array of posted data.
	 */
	protected function update_session( $data ) {
		$this->session->put( 'selected_payment_method', $data['payment_method'] );

		// Update reservation totals.
		// $this->reservation->calculate_totals();
	}

	/**
	 * Process a booking that does require payment.
	 *
	 * @param  \AweBooking\Model\Booking   $booking The booking instance.
	 * @param  \AweBooking\Gateway\Gateway $gateway The payment gateway.
	 * @param  \WPLibs\Http\Request        $request The http request.
	 *
	 * @return \AweBooking\Gateway\Response
	 *
	 * @throws GatewayException
	 */
	protected function process_payment( Booking $booking, Gateway $gateway, $request ) {
		// Store the booking ID in session so it can be re-used after payment failure.
		$this->session->put( 'booking_awaiting_payment', $booking->get_id() );

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
	 * Process a booking that doesn't require payment.
	 *
	 * @param  \AweBooking\Model\Booking $booking The booking instance.
	 * @return \AweBooking\Gateway\Response
	 */
	protected function process_without_payment( Booking $booking ) {
		$booking->update_status( apply_filters( 'abrs_booking_status_without_payment', 'on-hold' ) );

		return ( new Gateway_Response( 'success' ) )->data( $booking );
	}

	/**
	 * Create a booking from trusted data.
	 *
	 * @param  \AweBooking\Support\Fluent $data The posted data.
	 * @return int
	 */
	public function create_booking( $data ) {
		// Give plugins the opportunity to create an booking themselves.
		if ( $_booking_id = apply_filters( 'abrs_checkout_create_booking', null, $this ) ) {
			return $_booking_id;
		}

		// If there is an booking pending payment, we can resume it here so
		// long as it has not changed. If the booking has changed, i.e.
		// different items or cost, create a new booking.
		if ( $booking = $this->get_awaiting_booking() ) {
			$this->resume_awating_booking( $booking );
		} else {
			$booking = new Booking;
		}

		// Fill the booking data.
		$booking->fill([
			'created_via'          => 'checkout',
			'customer_id'          => apply_filters( 'abrs_checkout_customer_id', get_current_user_id() ),
			'arrival_time'         => $data->get( 'arrival_time', '' ),
			'customer_note'        => $data->get( 'customer_note', '' ),
			'customer_first_name'  => $data->get( 'customer_first_name', '' ),
			'customer_last_name'   => $data->get( 'customer_last_name', '' ),
			'customer_address'     => $data->get( 'customer_address', '' ),
			'customer_address_2'   => $data->get( 'customer_address_2', '' ),
			'customer_city'        => $data->get( 'customer_city', '' ),
			'customer_state'       => $data->get( 'customer_state', '' ),
			'customer_postal_code' => $data->get( 'customer_postal_code', '' ),
			'customer_country'     => $data->get( 'customer_country', '' ),
			'customer_company'     => $data->get( 'customer_company', '' ),
			'customer_phone'       => $data->get( 'customer_phone', '' ),
			'customer_email'       => $data->get( 'customer_email', '' ),
			'language'             => $this->reservation->language,
			'currency'             => $this->reservation->currency,
			'hotel_id'             => $this->reservation->hotel,
			'customer_ip_address'  => abrs_http_request()->ip(),
			'customer_user_agent'  => abrs_http_request()->get_user_agent(),
		]);

		do_action( 'abrs_checkout_creating_booking', $booking, $data, $this );

		// Save the booking.
		$saved = $booking->save();

		// Leave if we have trouble in save booking.
		if ( ! $saved || ! $booking->exists() ) {
			return 0;
		}

		// Create the booking items.
		$this->create_booking_items( $booking, $data );
		$this->create_service_items( $booking, $data );
		$this->create_fees_items( $booking, $data );
		$this->create_taxes_items( $booking, $data );
		$this->create_payment_item( $booking, $data );

		// Re-calculate the totals.
		$booking->calculate_totals();

		// Sets the public token for user viewing.
		$public_token = sha1( $booking->get_id() . uniqid( time(), true ) );
		$booking->set_public_token( $public_token );

		do_action( 'abrs_checkout_update_booking_meta', $booking->get_id(), $data );

		return $booking->get_id();
	}

	/**
	 * Get current payment method (store in session).
	 *
	 * @return string
	 */
	public function get_current_payment_method() {
		return $this->session->get( 'selected_payment_method' );
	}

	/**
	 * Determines if have any awaiting booking need to re-process.
	 *
	 * @return \AweBooking\Model\Booking|null
	 */
	public function get_awaiting_booking() {
		$awaiting_booking = $this->session->get( 'booking_awaiting_payment' );

		if ( empty( $awaiting_booking ) ) {
			return null;
		}

		$booking = abrs_get_booking( $awaiting_booking );
		if ( ! $booking ) {
			return null;
		}

		if ( ! in_array( $booking->get_status(), [ 'pending', 'failed' ] ) ) {
			return null;
		}

		return $booking;
	}

	/**
	 * Perform resume awating booking in the session.
	 *
	 * @param  \AweBooking\Model\Booking $booking The awating booking instance.
	 * @return void
	 */
	protected function resume_awating_booking( $booking ) {
		$booking->remove_items();

		do_action( 'abrs_resume_booking', $booking );
	}

	/**
	 * Create the booking items.
	 *
	 * @param  \AweBooking\Model\Booking  $booking The booking instance.
	 * @param  \AweBooking\Support\Fluent $data    The posted data.
	 * @return void
	 */
	public function create_booking_items( $booking, $data ) {
		foreach ( $this->reservation->get_room_stays() as $item_key => $room_stay ) {
			$room_rate = $room_stay->get_data();

			if ( ! $room_rate || ! $room_rate instanceof Room_Rate ) {
				continue;
			}

			$assign_rooms = $room_rate
				->get_remain_rooms()
				->take( $room_stay->quantity )
				->pluck( 'resource' )
				->values();

			$i = 1;
			$request = $room_rate->get_request();

			/* @var \AweBooking\Model\Room $room */
			foreach ( $assign_rooms as $room ) {
				$item = ( new Room_Item )->fill([
					'booking_id'   => $booking->get_id(),
					/* translators: The room number */
					'name'         => $room->get( 'name' ),
					'room_id'      => $room->get_id(),
					'room_type_id' => $room_rate->get_room_type()->get_id(),
					'rate_plan_id' => $room_rate->get_rate_plan()->get_id(),
					'check_in'     => $request->check_in,
					'check_out'    => $request->check_out,
					'adults'       => $request->adults ?: 1,
					'children'     => $request->children ?: 0,
					'infants'      => $request->infants ?: 0,
					'subtotal'     => $room_stay->get_subtotal(),
					'total_tax'    => $room_stay->get_tax(),
					'total'        => $room_stay->get_price_tax(),
					'taxes'        => [ 'total' => $room_stay->get_tax_rates() ],
				]);

				do_action( 'abrs_checkout_creating_booking_room_item', $item, $room_stay, $item_key, $booking );

				try {
					$item->save();
				} catch ( \Exception $e ) {
					abrs_report( $e );
				}

				$i++;
			}

			do_action( 'abrs_checkout_process_room_stay', $room_stay, $item_key, $booking );
		}
	}

	/**
	 * Create the booking items.
	 *
	 * @param  \AweBooking\Model\Booking  $booking The booking instance.
	 * @param  \AweBooking\Support\Fluent $data    The posted data.
	 * @return void
	 */
	public function create_service_items( $booking, $data ) {
		/* @var \AweBooking\Reservation\Item $res_item */
		foreach ( $this->reservation->get_services() as $item_key => $res_item ) {
			/* @var \AweBooking\Model\Service $service */
			if ( ! $service = $res_item->model() ) {
				continue;
			}

			$item = ( new Service_item )->fill([
				'name'       => $service->get( 'name' ),
				'booking_id' => $booking->get_id(),
				'service_id' => $service->get_id(),
				'quantity'   => $res_item->get_quantity(),
				'subtotal'   => $res_item->get_subtotal(),
				'total'      => $res_item->get_total(),
			]);

			try {
				$item->save();
			} catch ( \Exception $e ) {
				abrs_report( $e );
			}
		}
	}

	/**
	 * Create the booking fee items.
	 *
	 * @param  \AweBooking\Model\Booking  $booking The booking instance.
	 * @param  \AweBooking\Support\Fluent $data    The posted data.
	 * @return void
	 */
	public function create_fees_items( $booking, $data ) {
		/* @var \AweBooking\Reservation\Item $res_item */
		foreach ( $this->reservation->get_fees() as $item_key => $res_item ) {
			$item = ( new Fee_Item )->fill([
				'name'       => $res_item->get_name(),
				'booking_id' => $booking->get_id(),
				'total'      => $res_item->get_total(),
				'amount'     => $res_item->get_price(),
			]);

			try {
				$item->save();
			} catch ( \Exception $e ) {
				abrs_report( $e );
			}
		}
	}

	/**
	 * Create the booking tax items.
	 *
	 * @param  \AweBooking\Model\Booking  $booking The booking instance.
	 * @param  \AweBooking\Support\Fluent $data    The posted data.
	 * @return void
	 */
	public function create_taxes_items( $booking, $data ) {
		foreach ( $this->reservation->get_taxes() as $tax_rate ) {
			$item = ( new Tax_Item )->fill([
				'name'          => $tax_rate['name'],
				'booking_id'    => $booking->get_id(),
				'rate_id'       => $tax_rate['id'],
				'rate_amount'   => $tax_rate['rate'],
				'rate_compound' => $tax_rate['compound'],
			]);

			try {
				$item->save();
			} catch ( \Exception $e ) {
				abrs_report( $e );
			}
		}
	}

	/**
	 * Create the booking tax items.
	 *
	 * @param  \AweBooking\Model\Booking  $booking The booking instance.
	 * @param  \AweBooking\Support\Fluent $data    The posted data.
	 * @return void
	 */
	public function create_payment_item( $booking, $data ) {
		if ( empty( $data['payment_method'] ) ) {
			return;
		}

		$gateway = $this->gateways->get( $data['payment_method'] );

		if ( $gateway && method_exists( $gateway, 'new_payment' ) ) {
			$gateway->new_payment( $booking, $data );
		}
	}

	/**
	 * Create a new customer account if needed.
	 *
	 * @param  \AweBooking\Support\Fluent $data The posted data.
	 *
	 * @throws \Exception
	 */
	protected function process_customer( $data ) {
		do_action( 'abrs_checkout_process_customer_data', $data );
	}

	/**
	 * Validates the posted checkout data based on field properties.
	 *
	 * @param  \WP_Error                  $errors WP_Error instance.
	 * @param  \AweBooking\Support\Fluent $data   The posted data.
	 */
	protected function validate_posted_data( &$errors, $data ) {
		$controls = $this->get_controls();

		$validator = new Validator( $data->all() );

		foreach ( $controls->prop( 'fields' ) as $args ) {
			$key     = $args['id'];
			$control = $controls->get_field( $key );

			if ( $control->prop( 'required' ) && abrs_blank( $data[ $key ] ) ) {
				/* translators: %s Field name */
				$errors->add( 'required', sprintf( __( '%s is a required field.', 'awebooking' ), '<strong>' . esc_html( $control->prop( 'name' ) ) . '</strong>' ) );
			}

			if ( $control->prop( 'validate' ) ) {
				$validator->add_rule( $key, $control->prop( 'validate' ) );
			}
		}

		do_action( 'abrs_validate_checkout_data', $validator, $errors, $data );

		if ( $validator->fails() ) {
			$errors->add( 'validate', Arr::first( Arr::first( $validator->errors() ) ) );
		}

		do_action( 'abrs_validate_checkout_posted_data', $errors, $data );
	}

	/**
	 * Validates that the checkout has enough info to proceed.
	 *
	 * @param  \WP_Error                  $errors  WP_Error instance.
	 * @param  \AweBooking\Support\Fluent $data    The posted data.
	 * @param  \WPLibs\Http\Request    $request The http request.
	 */
	protected function validate_checkout( &$errors, $data, $request ) {
		if ( empty( $data['terms'] ) && apply_filters( 'abrs_checkout_show_terms', abrs_get_page_id( 'terms' ) > 0 ) ) {
			$errors->add( 'terms', esc_html__( 'You must accept our Terms &amp; Conditions.', 'awebooking' ) );
		}

		if ( ! empty( $data['payment_method'] ) ) {
			$gateway = $this->gateways->get( $data['payment_method'] );

			if ( $gateway ) {
				$response = $gateway->validate_fields( $data, $request );

				if ( is_wp_error( $response ) ) {
					$errors->add( 'gateway', $response->get_error_message() );
				} elseif ( false === $response ) {
					$errors->add( 'gateway', esc_html__( 'Sorry, there was an error processing your payment. Please try again later.', 'awebooking' ) );
				}
			} else {
				$errors->add( 'payment', esc_html__( 'Please choose a payment method.', 'awebooking' ) );
			}
		}

		do_action( 'abrs_validate_checkout', $errors, $data, $request );
	}

	/**
	 * Get posted data from the checkout form.
	 *
	 * @param  \WPLibs\Http\Request $request The http request.
	 * @return \AweBooking\Support\Fluent
	 */
	public function get_posted_data( $request ) {
		// Get all sanitized of controls data.
		$data = $this->get_controls()->handle( $request );

		$data['terms'] = $request->filled( 'terms' );
		$data['payment_method'] = abrs_clean( $request->get( 'payment_method' ) );

		return apply_filters( 'abrs_checkout_posted_data', $data, $request );
	}

	/**
	 * Gets the checkout controls.
	 *
	 * @return \AweBooking\Checkout\Form_Controls
	 */
	public function get_controls() {
		if ( is_null( $this->controls ) ) {
			$this->controls = apply_filters( 'abrs_checkout_controls', new Form_Controls( new Fluent( $this->session->get_old_input() ) ) );
			$this->controls->enabled()->prepare_fields();
		}

		return $this->controls;
	}
}
