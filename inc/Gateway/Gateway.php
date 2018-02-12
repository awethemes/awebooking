<?php
namespace AweBooking\Gateway;

use Awethemes\Http\Request;
use AweBooking\Model\Booking;
use AweBooking\Model\Booking_Payment_Item;

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
	protected $enabled = true;

	/**
	 * Gateway method title.
	 *
	 * @var string
	 */
	protected $method_title = '';

	/**
	 * Gateway method description.
	 *
	 * @var string
	 */
	protected $method_description = '';

	/**
	 * Name of gateway (front-end).
	 *
	 * @var string
	 */
	protected $title = '';

	/**
	 * Gateway description (front-end).
	 *
	 * @var string
	 */
	protected $description = '';

	/**
	 * The admin setting fields.
	 *
	 * @var array
	 */
	protected $setting_fields;

	/**
	 * The extra metadata this gateway support.
	 *
	 * Support: "transaction_id", "credit_card"
	 *
	 * @var array
	 */
	protected $supports = [];

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
		return (bool) $this->enabled;
	}

	/**
	 * Return the title for admin screens.
	 *
	 * @return string
	 */
	public function get_method_title() {
		return apply_filters( 'awebooking/gateway/get_method_title', $this->method_title, $this );
	}

	/**
	 * Return the description for admin screens.
	 *
	 * @return string
	 */
	public function get_method_description() {
		return apply_filters( 'awebooking/gateway/get_method_description', $this->method_description, $this );
	}

	/**
	 * Return the gateway's title.
	 *
	 * @return string
	 */
	public function get_title() {
		return apply_filters( 'awebooking/gateway/get_title', $this->title, $this->method );
	}

	/**
	 * Return the gateway's description.
	 *
	 * @return string
	 */
	public function get_description() {
		return apply_filters( 'awebooking/gateway/get_description', $this->description, $this->method );
	}

	/**
	 * Get the gateway supports.
	 *
	 * @return array
	 */
	public function get_supports() {
		return apply_filters( 'awebooking/gateway/get_supports', $this->supports, $this );
	}

	/**
	 * Determine if the gateway support a given meta field.
	 *
	 * @param  string|array $meta An array keys or a string of special key.
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
		return apply_filters( 'awebooking/gateway/get_description', $this->setting_fields, $this );
	}

	/**
	 * Determines if gateway has settings.
	 *
	 * @return boolean
	 */
	public function has_settings() {
		return ! empty( $this->get_setting_fields() );
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
	 * @return \AweBooking\Gateway\Response
	 */
	abstract public function process( Booking $booking );

	/**
	 * Get the return url (thank you page).
	 *
	 * @param  \AweBooking\Model\Booking|int|null $booking Optional, the booking instance or booking ID.
	 * @return string
	 */
	public function get_return_url( $booking = null ) {
		return '';
	}

	/**
	 * Print the payment fields in the front-end.
	 *
	 * @return void
	 */
	public function print_payment_fields() {
		if ( $description = $this->get_description() ) {
			echo wp_kses_post( wpautop( wptexturize( $description ) ) );
		}
	}

	/**
	 * Validate frontend payment fields.
	 *
	 * @param  \Awethemes\Http\Request $request The request instance.
	 * @return bool
	 */
	public function validate_payment_fields( Request $request ) {
		return true;
	}

	/**
	 * Display the payment content in admin.
	 *
	 * @return void
	 */
	public function display_payment_contents( Booking_Payment_Item $payment_item, Booking $booking ) {

		if ( $this->is_support( 'transaction_id' ) && $transaction_id = $payment_item->get_transaction_id() ) {
			echo '<strong>' . esc_html__( 'Transaction ID:', 'awebooking' ) . '</strong> '. esc_html( $transaction_id );
		}
	}

	/**
	 * Get the option by key (no prefix include).
	 *
	 * @param  string $key     The key.
	 * @param  mixed  $default The default value.
	 * @return mixed
	 */
	protected function get_option( $key, $default = null ) {
		$prefix = sanitize_key( 'gateway_' . $this->get_method() );

		return awebooking_option( $prefix . '_' . $key, $default );
	}
}
