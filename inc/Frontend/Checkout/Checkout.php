<?php
namespace AweBooking\Frontend\Checkout;

use WP_Error;
use Awethemes\Http\Request;
use Awethemes\WP_Session\WP_Session;
use AweBooking\Reservation\Reservation;

class Checkout {
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
	 * @param \Awethemes\WP_Session\WP_Session    $session     The WP_Session class instance.
	 * @param \AweBooking\Reservation\Reservation $reservation The reservation instance.
	 */
	public function __construct( WP_Session $session, Reservation $reservation ) {
		$this->reservation = $reservation;
		$this->session     = $session;
	}

	/**
	 * Process the checkout request.
	 *
	 * @param  \Awethemes\Http\Request $request The http request.
	 * @return mixed
	 */
	public function process( Request $request ) {
		$errors = new WP_Error();
		$data   = $this->get_posted_data( $request );

		do_action( 'awebooking/checkout_process', $data, $errors );

		// Update session for customer and totals.
		$this->update_session( $data );

		$this->validate_posted_data( $data, $errors );

		foreach ( $errors->get_error_messages() as $message ) {
			// abrs_add_notice( $message, 'error' );
		}

		if ( empty( $errors->errors ) ) {
			dd($data);
			// $booking = $this->create_booking();

			// $this->process_order_payment( $order_id, $posted_data['payment_method'] );
		}

		dd( $errors );
	}

	/**
	 * Create an order.
	 *
	 * @param  $data Posted data.
	 * @return int|WP_ERROR
	 */
	public function create_order( $data ) {
		// Give plugins the opportunity to create an order themselves.
		if ( $order_id = apply_filters( 'awebooking_create_order', null, $this ) ) {
			return $order_id;
		}

		$order_id           = absint( WC()->session->get( 'order_awaiting_payment' ) );
		$cart_hash          = md5( json_encode( wc_clean( WC()->cart->get_cart_for_session() ) ) . WC()->cart->total );

		$http_request       = abrs_http_request();
		$available_gateways = WC()->payment_gateways->get_available_payment_gateways();

		/**
		 * If there is an booking pending payment, we can resume it here so
		 * long as it has not changed. If the order has changed, i.e.
		 * different items or cost, create a new order. We use a hash to
		 * detect changes which is based on cart items + order total.
		 */
		if ( $order_id && ( $order = wc_get_order( $order_id ) ) && $order->has_cart_hash( $cart_hash ) && $order->has_status( array( 'pending', 'failed' ) ) ) {
			// Action for 3rd parties.
			do_action( 'awebooking_resume_order', $order_id );

			// Remove all items - we will re-add them later.
			$order->remove_order_items();
		} else {
			$order = new Booking;
		}

		// Fill the booking data.
		$booking->fill([
			'created_via'             => 'checkout',
			'customer_id'             => apply_filters( 'awebooking/checkout/customer_id', get_current_user_id() ),
			'arrival_time'            => $data['arrival_time'],
			'customer_note'           => $data['customer_note'],

			'customer_first_name'     => $data['billing_first_name'],
			'customer_last_name'      => $data['billing_last_name'],
			'customer_address'        => $data['billing_address'],
			'customer_address_2'      => $data['billing_address_2'],
			'customer_city'           => $data['billing_city'],
			'customer_state'          => $data['billing_state'],
			'customer_postal_code'    => $data['billing_postal_code'],
			'customer_country'        => $data['billing_country'],
			'customer_company'        => $data['billing_company'],
			'customer_phone'          => $data['billing_phone'],
			'customer_email'          => $data['billing_email'],

			'language'                => abrs_running_on_multilanguage() ? awebooking( 'multilingual' )->get_current_language() : '',
			'currency'                => abrs_current_currency(),
			'customer_ip_address'     => $http_request->ip(),
			'customer_user_agent'     => $http_request->get_user_agent(),
		]);

		// $order->set_cart_hash( $cart_hash );
		// $order->set_prices_include_tax( 'yes' === get_option( 'awebooking_prices_include_tax' ) );

		// $order->set_shipping_total( WC()->cart->get_shipping_total() );
		// $order->set_discount_total( WC()->cart->get_discount_total() );
		// $order->set_discount_tax( WC()->cart->get_discount_tax() );
		// $order->set_cart_tax( WC()->cart->get_cart_contents_tax() + WC()->cart->get_fee_tax() );
		// $order->set_shipping_tax( WC()->cart->get_shipping_tax() );
		// $order->set_total( WC()->cart->get_total( 'edit' ) );

		$this->create_order_line_items( $order, WC()->cart );
		$this->create_order_fee_lines( $order, WC()->cart );
		$this->create_order_shipping_lines( $order, WC()->session->get( 'chosen_shipping_methods' ), WC()->shipping->get_packages() );
		$this->create_order_tax_lines( $order, WC()->cart );
		$this->create_order_coupon_lines( $order, WC()->cart );

		/**
		 * Action hook to adjust order before save.
		 * @since 3.0.0
		 */
		do_action( 'awebooking_checkout_create_order', $order, $data );

		// Save the order.
		$order_id = $order->save();

		do_action( 'awebooking_checkout_update_order_meta', $order_id, $data );

		return $order_id;
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
	 * Validates the posted checkout data based on field properties.
	 *
	 * @param  array    $data   An array of posted data.
	 * @param  WP_Error $errors WP_Error instance.
	 */
	protected function validate_posted_data( $data, WP_Error $errors ) {
		$controls = $this->get_controls();

		foreach ( $controls->prop( 'fields' ) as $args ) {
			$key     = $args['id'];
			$control = $controls->get_field( $key );

			if ( $control->prop( 'required' ) && abrs_blank( $data[ $key ] ) ) {
				/* translators: %s Field name */
				$errors->add( 'required-field', sprintf( __( '%s is a required field.', 'awebooking' ), '<strong>' . esc_html( $control->prop( 'name' ) ) . '</strong>' ), $control->prop( 'name' ) );
			}
		}
	}

	/**
	 * Get posted data from the checkout form.
	 *
	 * @param  \Awethemes\Http\Request $request The http request.
	 * @return array
	 */
	public function get_posted_data( Request $request ) {
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
			$this->setup_controls();
		}

		return $this->controls;
	}

	/**
	 * Setup checkout fields.
	 *
	 * @return void
	 */
	protected function setup_controls() {
		$controls = abrs_create_form( 'checkout', abrs_http_request() );

		// Billing fields.
		$billing = $controls->add_section( 'billing' );

		foreach ( $this->get_billing_controls() as $key => $args ) {
			$billing->add_field( array_merge( $args, [ 'id' => 'billing_' . $key ] ) );
		}

		// Booking fields.
		$additionals = $controls->add_section( 'additionals' );

		$additionals->add_field([
			'id'               => 'arrival_time',
			'type'             => 'select',
			'name'             => esc_html__( 'Estimated time of arrival', 'awebooking' ),
			'options_cb'       => 'abrs_list_hours',
			'classes'          => 'with-selectize',
			'show_option_none' => esc_html__( 'I don\'t know', 'awebooking' ),
			'sanitization_cb'  => 'absint',
		]);

		$additionals->add_field([
			'id'               => 'customer_note',
			'type'             => 'textarea',
			'name'             => esc_html__( 'Special requests', 'awebooking' ),
			'sanitization_cb'  => 'sanitize_textarea_field',
		]);

		$this->controls = apply_filters( 'awebooking/checkout/controls', $controls );
		$this->controls->prepare_fields();
	}

	/**
	 * Gets the billing fields.
	 *
	 * @see https://github.com/CMB2/CMB2/wiki/Field-Parameters
	 *
	 * @return array
	 */
	protected function get_billing_controls() {
		return apply_filters( 'awebooking/checkout/billing_controls', [
			'first_name' => [
				'type'         => 'text',
				'name'         => esc_html__( 'First name', 'awebooking' ),
				'required'     => true,
				'attributes'   => [
					'autofocus'    => true,
					'autocomplete' => 'given-name',
				],
			],
			'last_name' => [
				'type'         => 'text',
				'name'         => esc_html__( 'Last name', 'awebooking' ),
				'required'     => true,
				'attributes'   => [ 'autocomplete' => 'family-name' ],
			],
			'company' => [
				'type'         => 'text',
				'name'         => esc_html__( 'Company name', 'awebooking' ),
				'classes'      => [ 'form-row-wide' ],
				'attributes'   => [ 'autocomplete' => 'organization' ],
			],
			'country' => [
				'type'         => 'select',
				'name'         => esc_html__( 'Country', 'awebooking' ),
				'classes'      => 'with-selectize',
				'options_cb'   => 'abrs_list_countries',
				'attributes'   => [ 'autocomplete' => 'country' ],
				'show_option_none' => '---',
			],
			'address' => [
				'type'         => 'text',
				'name'         => esc_html__( 'Street address', 'awebooking' ),
				'required'     => true,
				'attributes'   => [
					'autocomplete' => 'address-line1',
					'placeholder'  => esc_html__( 'House number and street name', 'awebooking' ),
				],
			],
			'address_2' => [
				'type'         => 'text',
				'attributes'   => [
					'placeholder'  => esc_html__( 'Apartment, suite, unit etc. (optional)', 'awebooking' ),
					'autocomplete' => 'address-line2',
				],
			],
			'city' => [
				'type'         => 'text',
				'name'         => esc_html__( 'Town / City', 'awebooking' ),
				'required'     => true,
				'attributes'   => [ 'autocomplete' => 'address-level2' ],
			],
			'state' => [
				'type'         => 'text',
				'name'         => esc_html__( 'State / County', 'awebooking' ),
				'required'     => true,
				'attributes'   => [ 'autocomplete' => 'address-level1' ],
			],
			'postal_code' => [
				'type'         => 'text',
				'name'         => esc_html__( 'Postcode / ZIP', 'awebooking' ),
				'required'     => true,
				'attributes'   => [ 'autocomplete' => 'postal-code' ],
			],
			'phone' => [
				'type'         => 'text',
				'name'         => esc_html__( 'Phone number', 'awebooking' ),
				'required'     => true,
				'attributes'   => [ 'autocomplete' => 'tel' ],
			],
			'email' => [
				'type'             => 'text',
				'name'             => esc_html__( 'Email address', 'awebooking' ),
				'required'         => true,
				'sanitization_cb'  => 'sanitize_email',
				'attributes'       => [
					'type'         => 'email',
					'autocomplete' => 'email',
				],
			],
		]);
	}
}
