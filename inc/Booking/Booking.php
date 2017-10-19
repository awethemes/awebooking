<?php
namespace AweBooking\Booking;

use AweBooking\AweBooking;
use AweBooking\Pricing\Price;
use AweBooking\Support\WP_Object;
use AweBooking\Support\Period_Collection;
use AweBooking\Support\Period;

class Booking extends WP_Object {
	use Traits\Booking_Items_Trait,
		Traits\Booking_Attributes_Trait;

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
		'featured'                => false,
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
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'total'          => 'float',
		'discount_total' => 'float',
		'checked_in'     => 'bool',
		'checked_out'    => 'bool',
		'featured'       => 'bool',
		'customer_id'    => 'integer',
	];

	/**
	 * Booking constructor.
	 *
	 * @param mixed $booking The booking ID we'll working for.
	 */
	public function __construct( $booking = 0 ) {
		$this->maping_metadata();

		parent::__construct( $booking );
	}

	/**
	 * Setup the object attributes.
	 *
	 * @return void
	 */
	protected function setup() {
		$this['status']        = $this->instance->post_status;
		$this['date_created']  = $this->instance->post_date;
		$this['date_modified'] = $this->instance->post_modified;
	}

	/**
	 * Calculate and returns booking subtotal.
	 *
	 * @return float
	 */
	public function get_subtotal() {
		$subtotal = 0;

		foreach ( $this->get_line_items() as $item ) {
			$subtotal += $item->get_subtotal();
		}

		return apply_filters( $this->prefix( 'get_subtotal' ), $subtotal, $this );
	}

	/**
	 * Calculate totals by looking at the contents of the booking.
	 *
	 * @return float
	 */
	public function calculate_totals() {
		$total    = 0;
		$subtotal = 0;

		foreach ( $this->get_line_items() as $item ) {
			$subtotal += $item->get_subtotal();
			$total    += $item->get_total();
		}

		$this['discount_total'] = $subtotal - $total;
		$this['total']          = $total;
		$this->save();

		return $this;
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
	 * Determines this booking have multiple rooms.
	 *
	 * @return boolean
	 */
	public function is_multiple_rooms() {
		return $this->get_line_items()->count() > 1;
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
	 * Get room state status.
	 *
	 * @return int
	 */
	public function get_state_status() {
		switch ( $this->get_status() ) {
			case static::COMPLETED:
				return AweBooking::STATE_BOOKED;
			case static::CANCELLED:
				return AweBooking::STATE_AVAILABLE;
			default:
				return AweBooking::STATE_PENDING;
		}
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
	 * Returns the arrival date.
	 *
	 * @return Carbonate|null
	 */
	public function get_arrival_date() {
		$period = $this->get_period_collection()->collapse();

		return ! is_null( $period ) ? $period->get_start_date() : null;
	}

	/**
	 * Returns the departure date.
	 *
	 * @return Carbonate|null
	 */
	public function get_departure_date() {
		$period = $this->get_period_collection()->collapse();

		return ! is_null( $period ) ? $period->get_end_date() : null;
	}

	/**
	 * Returns nights stayed of this booking.
	 *
	 * @return int
	 */
	public function calculate_nights_stayed() {
		$nights = 0;
		foreach ( $this->get_line_items() as $key => $item ) {
			$nights += $item->get_nights_stayed();
		}

		return $nights;
	}

	public function get_price( $price ) {
		return new Price( $price, $this->get_currency() );
	}

	/**
	 * Gets formatted guest number HTML.
	 *
	 * @param  boolean $echo Echo or return output.
	 * @return string|void
	 */
	public function get_fomatted_guest_number( $echo = true ) {
		$adults = $children = 0;
		foreach ( $this->get_line_items() as $key => $item ) {
			$adults += $item->get_adults();
			$children += $item->get_children();
		}

		$html = '';

		$html .= sprintf(
			'<span class="">%1$d %2$s</span>',
			$adults,
			_n( 'adult', 'adults', $adults, 'awebooking' )
		);

		if ( $children ) {
			$html .= sprintf(
				' &amp; <span class="">%1$d %2$s</span>',
				$children,
				_n( 'child', 'children', $children, 'awebooking' )
			);
		}

		if ( $echo ) {
			print $html; // WPCS: XSS OK.
		} else {
			return $html;
		}
	}

	/**
	 * Determines periods of booking items is continuous.
	 *
	 * @return boolean
	 */
	public function is_continuous_periods() {
		return $this->get_period_collection()->is_continuous();
	}

	/**
	 * Returns Period collection of booking items.
	 *
	 * @return Period_Collection
	 */
	public function get_period_collection() {
		$periods = $this->get_line_items()->map(function( $item ) {
			return $item->get_period();
		})->values();

		return new Period_Collection( $periods->to_array() );
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

	/*
	| ------------------------------------------------------
	| Private zone
	| ------------------------------------------------------
	*/

	/**
	 * Do somethings when finish save.
	 *
	 * @return void
	 */
	protected function finish_save() {
		parent::finish_save();

		// Save items.
		$this->save_items();
	}

	/**
	 * Clean object cache after saved.
	 *
	 * @return void
	 */
	protected function clean_cache() {
		wp_cache_delete( $this->get_id(), 'awebooking_cache_booking_items' );
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
			'post_status'   => $this->get_status(),
			'post_title'    => $this->get_booking_title(),
			'post_excerpt'  => $this->get_customer_note(),
			'post_password' => awebooking_random_string(),
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
		$changes = $this->get_changes_only( $dirty, [ 'status', 'customer_note', 'date_created' ] );
		if ( empty( $changes ) ) {
			return true;
		}

		$this['status'] = $this['status'] ? $this['status'] : Booking::PENDING;

		return $this->update_the_post([
			'post_status'  => $this['status'],
			'post_excerpt' => $this['customer_note'],
			'post_date'    => $this['date_created'],
		]);
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
			'featured'                => '_featured',
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
		]);
	}
}
