<?php
namespace AweBooking;

use AweBooking\AweBooking;
use AweBooking\Pricing\Price;
use AweBooking\Currency\Currency;
use AweBooking\Support\WP_Object;
use AweBooking\Support\Date_Utils;
use AweBooking\Support\Date_Period;

class Booking extends WP_Object {
	/* Booking Status */
	const PENDING    = 'awebooking-pending';
	const PROCESSING = 'awebooking-inprocess';
	const COMPLETED  = 'awebooking-completed';
	const CANCELLED  = 'awebooking-cancelled';
	const FAILED     = 'awebooking-failed';

	/**
	 * Name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = AweBooking::BOOKING;

	/**
	 * The attributes for this object.
	 *
	 * Name value pairs (name + default value).
	 *
	 * @var array
	 */
	protected $attributes = [
		// Booking attributes.
		'status'                  => '',
		'currency'                => '',
		'version'                 => '',
		'date_created'            => null,
		'date_modified'           => null,
		'checked_in'              => false,
		'checked_out'             => false,
		'discount_total'          => 0,
		'total'                   => 0,

		// Customer attributes.
		'customer_id'             => 0,
		'customer_title'          => '',
		'customer_first_name'     => '',
		'customer_last_name'      => '',
		'customer_address'        => '',
		'customer_address_2'      => '',
		'customer_city'           => '',
		'customer_state'          => '',
		'customer_postal_code'    => '',
		'customer_country'        => '',
		'customer_company'        => '',
		'customer_phone'          => '',
		'customer_email'          => '',
		'customer_note'           => '',
		'customer_ip_address'     => '',
		'customer_user_agent'     => '',

		// Payments attributes.
		'payment_method'          => '',
		'payment_method_title'    => '',
		'transaction_id'          => '',
		'created_via'             => '',
		'date_paid'               => null,
		'date_completed'          => null,
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'total'          => 'float',
		'checked_in'     => 'bool',
		'checked_out'    => 'bool',
		'discount_total' => 'float',
		'customer_id'    => 'integer',
	];

	/**
	 * Booking items will be stored here.
	 *
	 * @var array
	 */
	protected $items = [];

	/**
	 * Booking items that need deleting are stored here.
	 *
	 * @var array
	 */
	protected $items_to_delete = [];

	/**
	 * Adds a booking item to this booking.
	 *
	 * The booking item will not persist until save.
	 *
	 * @param Booking_Item $item Booking item instance.
	 *
	 * @return true|void
	 */
	public function add_item( Booking_Item $item ) {
		$item_type = $item->get_type();

		// A line item must be have type name, so if someone
		// given invalid line item, we just leave and do nothing.
		if ( empty( $item_type ) ) {
			return;
		}

		// Set the booking ID for item.
		$item->set_booking_id( $this->get_id() );
		$this->items[] = $item;

		return true;
	}

	public function get_item( $item_id ) {
		foreach ( $this->items as $item ) {
			if ( $item_id === $item->get_id() ) {
				return $item;
			}
		}
	}

	public function has_item( $id ) {
		return ! is_null( $this->get_item( $id ) );
	}

	/**
	 * Save all boooking items which are part of this boooking.
	 */
	protected function save_items() {
		$saved = [];

		foreach ( array_filter( $this->items ) as $item ) {
			// To ensure, set booking ID again.
			$item->set_booking_id( $this->get_id() );

			if ( $item->save() ) {
				$saved[] = $item;
			}
		}

		return $saved;
	}

	public function get_room_items() {
		$aa = [];

		foreach ($this->items as $a ) {
			$aa[] = \AweBooking\Factory::get_booking_item( $a );
		}

		return $aa;
	}

	/**
	 * Create new Booking.
	 *
	 * @param mixed $booking Booking ID or booking object.
	 */
	public function __construct( $booking = 0 ) {
		$this->maps = $this->get_maping_metadata();

		parent::__construct( $booking );
	}

	public function get_edit_url( array $query_args = [], $context = 'raw' ) {
		return add_query_arg( $query_args,
			get_edit_post_link( $this->get_id(), $context )
		);
	}

	/**
	 * Get the booking number.
	 *
	 * @return int
	 */
	public function get_booking_id() {
		return apply_filters( $this->prefix( 'get_booking_id' ), $this->get_id(), $this );
	}

	/**
	 * Get the booking date time.
	 *
	 * @return \Carbon\Carbon
	 */
	public function get_booking_date() {
		return apply_filters( $this->prefix( 'get_booking_date' ), Date_Utils::create_datetime( $this['booking_date'] ), $this );
	}

	/**
	 * Get booking status.
	 *
	 * @return string
	 */
	public function get_status() {
		return apply_filters( $this->prefix( 'get_status' ), $this['status'], $this );
	}

	/**
	 * Gets order currency.
	 *
	 * @return string
	 */
	public function get_currency() {
		$currency = $this['currency'] ? new Currency( $this['currency'] ) : awebooking( 'currency' );

		return apply_filters( $this->prefix( 'get_currency' ), $currency, $this );
	}

	/**
	 * //
	 *
	 * @return Price
	 */
	public function get_total_price() {
		$price = new Price( $this['total'], $this->get_currency() );

		return apply_filters( $this->prefix( 'get_currency' ), $price, $this );
	}

	/**
	 * Get booking customer ID.
	 *
	 * @return int
	 */
	public function get_customer_id() {
		return $this->get_attribute( 'customer_id' );
	}

	/**
	 * Get booking customer company.
	 *
	 * @return string
	 */
	public function get_customer_company() {
		return $this->get_attribute( 'customer_company' );
	}

	public function get_customer_email() {
		return $this->get_attribute( 'customer_email' );
	}

	/**
	 * Get the payment method.
	 *
	 * @return string
	 */
	public function get_payment_method() {
		return apply_filters( $this->prefix( 'get_payment_method' ), $this['payment_method'], $this );
	}

	/**
	 * Get payment_method_title.
	 *
	 * @return string
	 */
	public function get_payment_method_title() {
		return apply_filters( $this->prefix( 'get_payment_method_title' ), $this['payment_method_title'], $this );
	}

	/**
	 * Get transaction_id.
	 *
	 * @return string
	 */
	public function get_transaction_id() {
		return apply_filters( $this->prefix( 'get_transaction_id' ), $this['transaction_id'], $this );
	}

	/**
	 * Get room state status.
	 *
	 * @return int
	 */
	public function get_state_status() {
		switch ( $this->get_status() ) {
			case static::COMPLETED:
				return Room_State::BOOKED;
			case static::CANCELLED:
				return Room_State::AVAILABLE;
			default:
				return Room_State::PENDING;
		}
	}

	/**
	 * Checks the order status against a passed in status.
	 *
	 * @param  string|array $status //.
	 * @return bool
	 */
	public function has_status( $status ) {
		$has_status = in_array( $this->get_status(), (array) $status );

		return apply_filters( $this->prefix( 'has_status' ), $has_status, $this );
	}

	/**
	 * Checks if an booking can be edited.
	 *
	 * @return bool
	 */
	public function is_editable() {
		$editable = in_array( $this->get_status(), [ static::PENDING, 'auto-draft' ] );

		return apply_filters( $this->prefix( 'is_editable' ), $editable, $this );
	}

	/**
	 * Get a title for the new post type.
	 *
	 * @return string
	 */
	protected function get_booking_title() {
		// @codingStandardsIgnoreStart
		/* translators: %s: Booking date */
		return sprintf( __( 'Order &ndash; %s', 'woocommerce' ), strftime( _x( '%b %d, %Y @ %I:%M %p', 'Booking date parsed by strftime', 'awebooking' ) ) );
		// @codingStandardsIgnoreEnd
	}

	/**
	 * Setup the object attributes.
	 *
	 * @return void
	 */
	protected function setup() {
		$this['status'] = $this->instance->post_status;
		$this['date_created'] = $this->instance->post_date;
		$this['date_modified'] = $this->instance->post_modified;

		if ( ! $this->items ) {
			global $wpdb;

			$get_items_sql = $wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}awebooking_booking_items` WHERE `booking_id` = %d ORDER BY `booking_item_id`;", $this->get_id() );
			$items         = $wpdb->get_results( $get_items_sql );

			$aa = [];
			foreach ($items as $a ) {
				$aa[] = \AweBooking\Factory::get_booking_item( $a );
			}

			$this->items = $aa;
		}
	}

	/**
	 * Do somethings when finish save.
	 *
	 * @return void
	 */
	protected function finish_save() {
		parent::finish_save();

		$this->save_items();
	}

	/**
	 * Run perform insert object into database.
	 *
	 * @see wp_insert_post()
	 *
	 * @return int|void
	 */
	protected function perform_insert() {
		$this['version']  = AweBooking::VERSION;
		$this['status']   = $this['status'] ? $this['status'] : Booking::PENDING;
		$this['currency'] = $this['currency'] ? $this['currency'] : awebooking( 'currency' )->get_code();

		$insert_id = wp_insert_post( apply_filters( $this->prefix( 'insert_data' ), [
			'post_type'     => $this->object_type,
			'post_status'   => $this['status'],
			'post_title'    => $this->get_booking_title(),
			'post_excerpt'  => $this['customer_note'],
			'post_password' => uniqid( 'booking_' ),
			'ping_status'   => 'closed',
			'post_author'   => 1,
			'post_parent'   => 0,
		]), true );

		if ( ! is_wp_error( $insert_id ) && $insert_id > 0 ) {
			return $insert_id;
		}
	}

	/**
	 * Run perform update object.
	 *
	 * @see WP_Object::update_the_post()
	 *
	 * @param  array $dirty The attributes has been modified.
	 * @return bool|void
	 */
	protected function perform_update( array $dirty ) {
		$changes = $this->get_changes_only( $dirty, [ 'status', 'customer_note' ] );
		if ( empty( $changes ) ) {
			return;
		}

		$this['status'] = $this['status'] ? $this['status'] : Booking::PENDING;

		return $this->update_the_post([
			'post_status'  => $this['status'],
			'post_excerpt' => $this['customer_note'],
		]);
	}

	/**
	 * Returns metadata maps array.
	 *
	 * @return array
	 */
	protected function get_maping_metadata() {
		return apply_filters( $this->prefix( 'maping_metadata' ), [
			'currency'                => '_currency',
			'version'                 => '_version',
			'checked_in'              => '_checked_in',
			'checked_out'             => '_checked_out',
			'discount_total'          => '_discount_total',
			'total'                   => '_total',

			'customer_id'             => '_customer_id',
			'customer_title'          => '_customer_title',
			'customer_first_name'     => '_customer_first_name',
			'customer_last_name'      => '_customer_last_name',
			'customer_address'        => '_customer_address',
			'customer_address_2'      => '_customer_address_2',
			'customer_city'           => '_customer_city',
			'customer_state'          => '_customer_state',
			'customer_postal_code'    => '_customer_postal_code',
			'customer_country'        => '_customer_country',
			'customer_company'        => '_customer_company',
			'customer_phone'          => '_customer_phone',
			'customer_email'          => '_customer_email',
			'customer_note'           => '_customer_note',
			'customer_ip_address'     => '_customer_ip_address',
			'customer_user_agent'     => '_customer_user_agent',

			'payment_method'          => '_payment_method',
			'payment_method_title'    => '_payment_method_title',
			'transaction_id'          => '_transaction_id',
			'created_via'             => '_created_via',
			'date_paid'               => '_date_paid',
			'date_completed'          => '_date_completed',
		]);
	}
}
