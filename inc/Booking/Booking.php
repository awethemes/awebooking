<?php
namespace AweBooking\Booking;

use AweBooking\Factory;
use AweBooking\AweBooking;
use AweBooking\Pricing\Price;
use AweBooking\Hotel\Room_State;
use AweBooking\Currency\Currency;
use AweBooking\Support\WP_Object;
use AweBooking\Support\Collection;
use AweBooking\Support\Carbonate;
use AweBooking\Support\Date_Period;

class Booking extends WP_Object {
	use Items\Booking_Item_Trait;

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
	 * Booking constructor.
	 *
	 * @param mixed $booking The booking ID we'll working for.
	 */
	public function __construct( $booking = 0 ) {
		$this->items = new Collection;

		$this->maping_metadata();

		parent::__construct( $booking );
	}

	/**
	 * Calculate totals by looking at the contents of the order. Stores the totals and returns the orders final total.
	 *
	 * @since 2.2
	 * @param  bool $and_taxes Calc taxes if true.
	 * @return float calculated grand total.
	 */
	public function calculate_totals( $and_taxes = true ) {
		$cart_subtotal     = 0;
		$cart_total        = 0;
		$fee_total         = 0;
		$cart_subtotal_tax = 0;
		$cart_total_tax    = 0;

		if ( $and_taxes ) {
			$this->calculate_taxes();
		}

		// line items
		foreach ( $this->get_items() as $item ) {
			$cart_subtotal     += $item->get_subtotal();
			$cart_total        += $item->get_total();
			$cart_subtotal_tax += $item->get_subtotal_tax();
			$cart_total_tax    += $item->get_total_tax();
		}

		$this->calculate_shipping();

		foreach ( $this->get_fees() as $item ) {
			$fee_total += $item->get_total();
		}

		$grand_total = round( $cart_total + $fee_total + $this->get_shipping_total() + $this->get_cart_tax() + $this->get_shipping_tax(), wc_get_price_decimals() );

		$this->set_discount_total( $cart_subtotal - $cart_total );
		$this->set_discount_tax( $cart_subtotal_tax - $cart_total_tax );
		$this->set_total( $grand_total );
		$this->save();

		return $grand_total;
	}

	public function get_subtotal() {
		$subtotal = 0;

		foreach ( $this->get_items() as $item ) {
			$subtotal += $item->get_total_price()->get_amount();
		}

		return $subtotal;
	}

	public function get_check_in() {
		$period = $this->merge_item_periods();

		return ! is_null( $period ) ? $period->get_start_date() : null;
	}

	public function get_check_out() {
		$period = $this->merge_item_periods();

		return ! is_null( $period ) ? $period->get_end_date() : null;
	}

	/**
	 * Merge item periods to a single period.
	 *
	 * @return Date_Period|null
	 */
	protected function merge_item_periods() {
		$items = $this->get_items();

		if ( count( $items ) === 1 ) {
			return $items[0]->get_date_period();
		}

		$period = null;
		foreach ( $items as $item ) {
			if ( is_null( $period ) ) {
				$period = $item->get_date_period();
				continue;
			}

			$period = $period->merge( $item->get_date_period() );
		}

		return $period;
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
		return apply_filters( $this->prefix( 'get_booking_date' ), Carbonate::create_datetime( $this['booking_date'] ), $this );
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
	 * Gets booking currency.
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
	 * Get customer ID.
	 *
	 * @return int
	 */
	public function get_customer_id() {
		return $this->get_attribute( 'customer_id' );
	}

	/**
	 * Get customer company.
	 *
	 * @return string
	 */
	public function get_customer_company() {
		return $this->get_attribute( 'customer_company' );
	}

	/**
	 * Get customer email address.
	 *
	 * @return string
	 */
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
	 * Checks the booking status against a passed in status.
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
	 * Adds a note (comment) to the booking. Booking must exist.
	 *
	 * @param  string $note              Note to add.
	 * @param  int    $is_customer_note  Is this a note for the customer?.
	 * @param  bool   $added_by_user     Was the note added by a user?.
	 * @return int
	 */
	public function add_booking_note( $note, $is_customer_note = 0, $added_by_user = false ) {
		if ( ! $this->get_id() ) {
			return 0;
		}

		if ( is_user_logged_in() && $added_by_user ) {
			$user                 = get_user_by( 'id', get_current_user_id() );
			$comment_author       = $user->display_name;
			$comment_author_email = $user->user_email;
		} else {
			$comment_author       = __( 'AweBooking', 'awebooking' );
			$comment_author_email = strtolower( __( 'AweBooking', 'awebooking' ) ) . '@';
			$comment_author_email .= isset( $_SERVER['HTTP_HOST'] ) ? str_replace( 'www.', '', $_SERVER['HTTP_HOST'] ) : 'noreply.com';
			$comment_author_email = sanitize_email( $comment_author_email );
		}

		$commentdata = apply_filters( 'awebooking/new_booking_note_data', array(
			'comment_post_ID'      => $this->get_id(),
			'comment_author'       => $comment_author,
			'comment_author_email' => $comment_author_email,
			'comment_author_url'   => '',
			'comment_content'      => $note,
			'comment_agent'        => 'AweBooking',
			'comment_type'         => 'booking_note',
			'comment_parent'       => 0,
			'comment_approved'     => 1,
		), array( 'booking_id' => $this->get_id(), 'is_customer_note' => $is_customer_note ) );

		$comment_id = wp_insert_comment( $commentdata );

		if ( $is_customer_note ) {
			add_comment_meta( $comment_id, 'is_customer_note', 1 );
			do_action( 'awebooking/new_customer_note', array( 'booking_id' => $this->get_id(), 'customer_note' => $commentdata['comment_content'] ) );
		}

		return $comment_id;
	}

	/**
	 * Returns edit url.
	 *
	 * @param  array  $query_args Extra query url.
	 * @param  string $context    See get_edit_post_link().
	 * @return string
	 */
	public function get_edit_url( array $query_args = [], $context = 'raw' ) {
		return add_query_arg( $query_args,
			get_edit_post_link( $this->get_id(), $context )
		);
	}

	/**
	 * Get a title for the new post type.
	 *
	 * @return string
	 */
	protected function get_booking_title() {
		// @codingStandardsIgnoreStart
		/* translators: %s: Booking date */
		return sprintf( __( 'Order &ndash; %s', 'awebooking' ), strftime( _x( '%b %d, %Y @ %I:%M %p', 'Booking date parsed by strftime', 'awebooking' ) ) );
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
	 * Mapping the metadata.
	 *
	 * @return void
	 */
	protected function maping_metadata() {
		$this->maps = apply_filters( $this->prefix( 'maping_metadata' ), [
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
