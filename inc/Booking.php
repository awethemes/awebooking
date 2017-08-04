<?php
namespace AweBooking;

use Exception;
use AweBooking\AweBooking;
use AweBooking\Pricing\Price;
use AweBooking\Currency\Currency;
use AweBooking\Support\WP_Object;
use AweBooking\Support\Date_Period;
use AweBooking\Support\Date_Utils;
use Roomify\Bat\Event\Event;

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
		// General attributes.
		'status'                  => '',   // Booking status.
		'date_paid'               => null, // TODO: In the future.
		'date_completed'          => null, // TODO: In the future.
		'booking_date'            => null, // The date booking, timestamp UNIX format.

		// Booking infomations.
		'adults'                  => 1,
		'children'                => 0,
		'check_in'                => '', // Y-m-d.
		'check_out'               => '', // Y-m-d.

		'room_id'                 => 0,
		'room_name'               => '',
		'room_type_id'            => 0,
		'room_type_title'         => '',
		'hotel_location'          => '', // Store slug of location.

		'request_services'        => [], // Service slug => number of request.

		// Cache price infomations.
		'currency'                => '',
		'room_total'              => 0, // Total price of services.
		'services_total'          => 0, // Total price of services.
		'total'                   => 0, // Price of room-type match with selection.

		// Customer infomations.
		'customer_id'             => 0,
		'customer_title'          => '',
		'customer_first_name'     => '',
		'customer_last_name'      => '',
		'customer_address'        => '',
		'customer_city'           => '', // In the future.
		'customer_state'          => '', // In the future.
		'customer_postal_code'    => '', // In the future.
		'customer_country'        => '', // In the future.
		'customer_company'        => '',
		'customer_phone'          => '',
		'customer_email'          => '',
		'customer_note'           => '',
		'customer_ip_address'     => '', // In the future.
		'customer_user_agent'     => '', // In the future.

		// Payments attributes.
		'payment_method'          => '',
		'payment_method_title'    => '',
		'transaction_id'          => '',
	];

	/**
	 * An array of meta data mapped with attributes.
	 *
	 * @var array
	 */
	protected $maps = [
		'customer_id',
		'customer_email',
		'customer_company',

		// TODO: ...
		'total' => 'total_price',
		'request_services' => 'booking_request_services',

		'adults'    => 'booking_adults',
		'children'  => 'booking_children',
		'check_in'  => 'booking_check_in',
		'check_out' => 'booking_check_out',

		'currency'        => 'booking_currency',
		'room_id'         => 'booking_room_id',
		'room_name'       => 'booking_room_name',
		'room_type_id'    => 'booking_room_type_id',
		'room_type_title' => 'booking_room_type_title',
		'hotel_location'  => 'booking_hotel_location',

		'payment_method'          => '_payment_method',
		'payment_method_title'    => '_payment_method_title',
		'transaction_id'          => '_payment_transaction_id',
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'adults'         => 'integer',
		'children'       => 'integer',
		'total'          => 'float',
		'room_total'     => 'float',
		'services_total' => 'float',
		'customer_id'    => 'integer',
	];

	/**
	 * Create new Booking.
	 *
	 * @param mixed $booking Booking ID or booking object.
	 */
	public function __construct( $booking = 0 ) {
		parent::__construct( $booking );
	}

	protected function perform_insert() {
	}

	protected function perform_update( array $changes ) {
	}

	/**
	 * Setup the object attributes.
	 *
	 * @return void
	 */
	protected function setup() {
		$this['status'] = $this->instance->post_status;

		if ( is_null( $this['booking_date'] ) ) {
			$this['booking_date'] = $this->instance->post_date;
		}

		// Setup the room infomation.
		// TODO: Seem this suck.
		if ( $this->is_booking_room_exists() ) {
			$the_room = $this->get_booking_room();

			$room_type = $the_room->get_room_type();
			$hotel_location = $room_type->get_location();

			$this['room_name']       = $the_room->get_name();
			$this['room_type_id']    = $room_type->get_id();
			$this['room_type_title'] = $room_type->get_title();
			$this['hotel_location']  = $hotel_location ? $hotel_location->slug : '';
		}
	}

	/**
	 * Save the booking.
	 *
	 * @return bool
	 */
	public function save() {
		if ( ! $this->exists ) {
			return false;
		}

		// Set room state.
		try {
			$date_period = new Date_Period( $this['check_in'], $this['check_out'], false );
		} catch ( Exception $e ) {
			return false;
		}

		update_post_meta( $this->id, 'customer_id', $this['customer_id'] );
		update_post_meta( $this->id, 'customer_note', $this['customer_note'] );
		update_post_meta( $this->id, 'customer_first_name', $this['customer_first_name'] );
		update_post_meta( $this->id, 'customer_last_name', $this['customer_last_name'] );
		update_post_meta( $this->id, 'customer_email', $this['customer_email'] );
		update_post_meta( $this->id, 'customer_phone', $this['customer_phone'] );
		update_post_meta( $this->id, 'customer_company', $this['customer_company'] );

		// Update check_in, check_out.
		update_post_meta( $this->id, 'booking_adults', $this['adults'] );
		update_post_meta( $this->id, 'booking_children', $this['children'] );
		update_post_meta( $this->id, 'booking_check_in', $this['check_in'] );
		update_post_meta( $this->id, 'booking_check_out', $this['check_out'] );

		update_post_meta( $this->id, 'currency', $this['currency'] );
		update_post_meta( $this->id, 'room_total', $this['room_total'] );
		update_post_meta( $this->id, 'total_price', $this['total'] );

		if ( $this['services_total'] ) {
			update_post_meta( $this->id, 'booking_request_services', $this['request_services'] );
			update_post_meta( $this->id, 'services_total', $this['services_total'] );
		}

		// Update the room infomations.
		$the_room = new Room( $this['room_id'] );

		// A room is mark exists it mean room-type exists too.
		// So we update the room title, room-type ID, room-type title...
		if ( $the_room->exists() ) {
			$room_type = $the_room->get_room_type();
			$hotel_location = $room_type->get_location();

			$this['room_name']       = $the_room->get_name();
			$this['room_type_id']    = $room_type->get_id();
			$this['room_type_title'] = $room_type->get_title();
			$this['hotel_location']  = $hotel_location ? $hotel_location->slug : '';

			// Update post meta.
			update_post_meta( $this->id, 'booking_room_id', $this['room_id'] );
			update_post_meta( $this->id, 'booking_room_name', $this['room_name'] );
			update_post_meta( $this->id, 'booking_room_type_id', $this['room_type_id'] );
			update_post_meta( $this->id, 'booking_room_type_title', $this['room_type_title'] );
			update_post_meta( $this->id, 'booking_hotel_location', $this['hotel_location'] );
		}

		// Delete all old state.
		// Bugs at here, if user change the date check-in, check-out.
		// TODO: ...

		// Update room state & store booking ID.
		awebooking( 'concierge' )->set_room_state( $the_room, $date_period, $this->get_state_status(), [
			'force' => true,
		]);

		$event = new Event( $date_period->get_start_date(), $date_period->get_end_date()->subMinute(), $the_room, $this['id'] );
		$event->saveEvent( awebooking( 'store.booking' ) );
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
	 * Gets booking currency.
	 *
	 * @return string
	 */
	public function get_currency() {
		$currency = $this['currency'] ? new Currency( $this['currency'] ) : awebooking( 'currency' );

		return apply_filters( $this->prefix( 'get_currency' ), $currency, $this );
	}

	/**
	 * Get booking number of adults.
	 *
	 * @return int
	 */
	public function get_adults() {
		return apply_filters( $this->prefix( 'get_adults' ), $this['adults'], $this );
	}

	/**
	 * Get booking number of children.
	 *
	 * @return int
	 */
	public function get_children() {
		return apply_filters( $this->prefix( 'get_children' ), $this['children'], $this );
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
	 * Get nights customer stays.
	 *
	 * @return int
	 */
	public function get_nights() {
		try {
			$date_period = new Date_Period( $this['check_in'], $this['check_out'], false );
			return $date_period->nights();
		} catch ( \Exception $e ) {
			return 0;
		}
	}

	public function get_booking_room() {
		if ( empty( $this['room_id'] ) ) {
			return;
		}

		// Cache the room.
		if ( ! $this['_the_room'] ) {
			$this['_the_room'] = new Room( $this['room_id'] );
		}

		return $this['_the_room'];
	}

	public function is_booking_room_exists() {
		$the_room = $this->get_booking_room();

		return ( ! is_null( $the_room ) && $the_room->exists() );
	}


	/**
	 * Get booking customer ID.
	 *
	 * @return int
	 */
	public function get_customer_id() {
		return $this->get_attr( 'customer_id' );
	}

	/**
	 * Get booking customer company.
	 *
	 * @return string
	 */
	public function get_customer_company() {
		return $this->get_attr( 'customer_company' );
	}

	public function get_customer_email() {
		return $this->get_attr( 'customer_email' );
	}

	public function get_hotel_location() {
		return $this->get_attr( 'hotel_location' );
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
		switch ( $this['status'] ) {
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
	 * @param string $note Note to add.
	 * @param int $is_customer_note (default: 0) Is this a note for the customer?
	 * @param  bool added_by_user Was the note added by a user?
	 * @return int Comment ID.
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
}
