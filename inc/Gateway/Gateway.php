<?php

namespace AweBooking\Gateway;

use AweBooking\Model\Booking;
use AweBooking\Model\Booking\Payment_Item;
use AweBooking\Checkout\Url_Generator;
use Awethemes\Http\Request;

abstract class Gateway {
	/**
	 * The gateway unique ID.
	 *
	 * @var string
	 */
	protected $method;

	/**
	 * Yes or no based on whether the method is enabled.
	 *
	 * @var string
	 */
	public $enabled = true;

	/**
	 * Gateway method title.
	 *
	 * @var string
	 */
	public $method_title = '';

	/**
	 * Gateway method description.
	 *
	 * @var string
	 */
	public $method_description = '';

	/**
	 * Name of gateway (front-end).
	 *
	 * @var string
	 */
	public $title = '';

	/**
	 * Gateway description (front-end).
	 *
	 * @var string
	 */
	public $description = '';

	/**
	 * The admin setting fields.
	 *
	 * @var array
	 */
	public $setting_fields = [];

	/**
	 * The extra metadata this gateway support.
	 *
	 * Support: "transaction_id", "credit_card".
	 *
	 * @var array
	 */
	public $supports = [];

	/**
	 * Get method name.
	 *
	 * @return string
	 */
	public function get_method() {
		return $this->method;
	}

	/**
	 * Determines if this gateway enable for using.
	 *
	 * @return boolean
	 */
	public function is_enabled() {
		return 'on' === abrs_sanitize_checkbox( $this->enabled );
	}

	/**
	 * Return the title for admin screens.
	 *
	 * @return string
	 */
	public function get_method_title() {
		return apply_filters( 'abrs_gateway_method_title', $this->method_title, $this );
	}

	/**
	 * Return the description for admin screens.
	 *
	 * @return string
	 */
	public function get_method_description() {
		return apply_filters( 'abrs_gateway_method_description', $this->method_description, $this );
	}

	/**
	 * Return the gateway's title.
	 *
	 * @return string
	 */
	public function get_title() {
		return apply_filters( 'abrs_gateway_title', $this->title, $this->method );
	}

	/**
	 * Return the gateway's description.
	 *
	 * @return string
	 */
	public function get_description() {
		return apply_filters( 'abrs_gateway_description', $this->description, $this->method );
	}

	/**
	 * Get the gateway supports.
	 *
	 * @return array
	 */
	public function get_supports() {
		return apply_filters( 'abrs_gateway_supports', $this->supports, $this );
	}

	/**
	 * Determine if the gateway support a given meta field.
	 *
	 * @param  string|array $meta An array keys or a string of specified key.
	 * @return bool
	 */
	public function is_support( $meta ) {
		$keys = is_array( $meta ) ? $meta : func_get_args();

		$supported = $this->get_supports();

		foreach ( $keys as $value ) {
			if ( ! in_array( $value, $supported ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Get the setting fields.
	 *
	 * @return array|null
	 */
	public function get_setting_fields() {
		return $this->setting_fields;
	}

	/**
	 * Determines if gateway has settings.
	 *
	 * @return boolean
	 */
	public function has_settings() {
		return ! empty( $this->setting_fields );
	}

	/**
	 * Get the option by key (no prefix include).
	 *
	 * @param  string $key     The key.
	 * @param  mixed  $default The default value.
	 * @return mixed
	 */
	public function get_option( $key, $default = null ) {
		$prefix = sanitize_key( 'gateway_' . $this->get_method() );

		if ( isset( $this->setting_fields[ $key ]['default'] ) && is_null( $default ) ) {
			$default = $this->setting_fields[ $key ]['default'];
		}

		return abrs_get_option( $prefix . '_' . $key, $default );
	}

	/**
	 * Setup the gateway.
	 *
	 * @return void
	 */
	abstract public function setup();

	/**
	 * Process payment.
	 *
	 * @param  \AweBooking\Model\Booking $booking The booking instance.
	 * @param  \WPLibs\Http\Request   $request The http request.
	 *
	 * @return \AweBooking\Gateway\Response
	 */
	abstract public function process( Booking $booking, Request $request );

	/**
	 * Get the return url (thank you page).
	 *
	 * @param int|\AweBooking\Model\Booking $booking The booking instance or booking ID.
	 * @return string
	 */
	public function get_return_url( $booking ) {
		$booking = abrs_get_booking( $booking );

		if ( ! $booking ) {
			return '';
		}

		return ( new Url_Generator( $booking ) )->get_booking_received_url();
	}

	/**
	 * Determines if the gateway has fields on the checkout.
	 *
	 * @return bool
	 */
	public function has_fields() {
		return false;
	}

	/**
	 * Print the payment fields in the front-end.
	 *
	 * @return void
	 */
	public function display_fields() {
		if ( $description = $this->get_description() ) {
			echo wp_kses_post( wpautop( wptexturize( $description ) ) );
		}
	}

	/**
	 * Validate frontend payment fields.
	 *
	 * @param  \AweBooking\Support\Fluent $data    The posted data.
	 * @param  \WPLibs\Http\Request    $request The http request.
	 *
	 * @return \WP_Error|bool
	 */
	public function validate_fields( $data, Request $request ) {
		return true;
	}

	/**
	 * Display the payment content in admin.
	 *
	 * @param \AweBooking\Model\Booking\Payment_Item $payment_item The payment item instance.
	 * @param \AweBooking\Model\Booking              $booking      The current booking instance.
	 *
	 * @return void
	 */
	public function display_payment_contents( Payment_Item $payment_item, Booking $booking ) {
		if ( $this->is_support( 'transaction_id' ) && $transaction_id = $payment_item->get( 'transaction_id' ) ) {
			echo '<strong>' . esc_html__( 'Transaction ID:', 'awebooking' ) . '</strong> ' . esc_html( $transaction_id );
		}
	}

	/**
	 * Create the payment item.
	 *
	 * @param  \AweBooking\Model\Booking $booking The booking instance.
	 * @param  mixed                     $data    The reservation data.
	 * @return Payment_Item
	 */
	public function create_new_payment( $booking, $data = [] ) {
		if ( ! $booking instanceof Booking ) {
			$booking = abrs_get_booking( $booking );
		}

		$payment_item = ( new Payment_Item )->fill( [
			'booking_id' => $booking->get_id(),
			'method'     => $this->get_method(),
			'amount'     => 0,
			'is_deposit' => 'off',
		] );

		try {
			$payment_item->save();
		} catch ( \Exception $e ) {
			abrs_report( $e );
		}

		return $payment_item;
	}
}
