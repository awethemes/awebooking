<?php
namespace AweBooking\Deprecated;

use AweBooking\AweBooking;
use AweBooking\Support\Period;
use AweBooking\Model\Room_Type;
use AweBooking\Booking\Request;
use AweBooking\Booking\Calendar;

trait Factory_Deprecated {
	/**
	 * Create booking calendar.
	 *
	 * @param  array   $rooms      Array of rooms.
	 * @param  integer $booking_id Booking ID.
	 * @return Calendar
	 */
	public static function create_booking_calendar( $rooms, $booking_id = 0 ) {
		$rooms = is_object( $rooms ) ? $rooms->all() : $rooms;

		return new Calendar( $rooms, awebooking( 'store.booking' ), $booking_id );
	}

	/**
	 * Create availability calendar.
	 *
	 * @param  array   $rooms         Array of rooms.
	 * @param  integer $default_state Default availability state.
	 * @return Calendar
	 */
	public static function create_availability_calendar( $rooms, $default_state = AweBooking::STATE_AVAILABLE ) {
		$rooms = is_object( $rooms ) ? $rooms->all() : $rooms;

		return new Calendar( $rooms, awebooking( 'store.availability' ), $default_state );
	}

	/**
	 * Create pricing calendar.
	 *
	 * @param  array   $rates         Array of rates.
	 * @param  integer $default_price Default rate price.
	 * @return Calendar
	 */
	public static function create_pricing_calendar( array $rates, $default_price = 0 ) {
		return new Calendar( $rates, awebooking( 'store.pricing' ), $default_price );
	}

	/**
	 * Create booking request from request data,
	 * If null given, using default $_REQUEST.
	 *
	 * @param  array $request An array of request data.
	 * @return Booking_Request
	 *
	 * @throws RuntimeException
	 */
	public static function create_booking_request( array $request = null ) {
		if ( is_null( $request ) ) {
			$request = $_REQUEST;
		}

		$start_date = isset( $request['start-date'] ) ? $request['start-date'] :
			( isset( $request['start_date'] ) ? $request['start_date'] : null );

		$end_date = isset( $request['end-date'] ) ? $request['end-date'] :
			( isset( $request['end_date'] ) ? $request['end_date'] : null );

		if ( empty( $start_date ) || empty( $start_date ) ) {
			throw new \RuntimeException( esc_html__( 'Start date and end date must be shown.', 'awebooking' ) );
		}

		$period = new Period(
			sanitize_text_field( wp_unslash( $start_date ) ),
			sanitize_text_field( wp_unslash( $end_date ) ),
			true
		);

		// Take accept requests.
		$booking_requests = [];
		$accept_requests  = [ 'adults', 'location', 'room-type' ];

		if ( awebooking( 'setting' )->is_children_bookable() ) {
			$accept_requests[] = 'children';
		}

		if ( awebooking( 'setting' )->is_infants_bookable() ) {
			$accept_requests[] = 'infants';
		}

		// Loop through the accept_requests and build booking_requests.
		foreach ( $accept_requests as $id ) {
			$booking_requests[ $id ] = isset( $request[ $id ] ) ? sanitize_text_field( wp_unslash( $request[ $id ] ) ) : null;
		}

		// Validate some request.
		if ( ! is_null( $booking_requests['adults'] ) ) {
			$booking_requests['adults'] = absint( $booking_requests['adults'] );
		}

		if ( ! empty( $booking_requests['children'] ) ) {
			$booking_requests['children'] = absint( $booking_requests['children'] );
		}

		if ( ! empty( $booking_requests['infants'] ) ) {
			$booking_requests['infants'] = absint( $booking_requests['infants'] );
		}

		return new Request( $period, $booking_requests );
	}

	/**
	 * Create availability from request data
	 * If null given, using default $_REQUEST.
	 *
	 * @param  array $request An array of request data.
	 * @return static
	 *
	 * @throws RuntimeException
	 */
	public static function create_room_from_request( array $request = null ) {
		if ( is_null( $request ) ) {
			$request = $_REQUEST;
		}

		// Trying get room-type ID from request.
		$room_type_id = isset( $request['room'] ) ? sanitize_text_field( wp_unslash( $request['room'] ) ) : 0;
		if ( empty( $room_type_id ) && isset( $request['room-type'] ) ) {
			$room_type_id = sanitize_text_field( wp_unslash( $request['room-type'] ) );
		}

		if ( empty( $room_type_id ) && isset( $request['room_type'] ) ) {
			$room_type_id = sanitize_text_field( wp_unslash( $request['room_type'] ) );
		}

		// Validation room-id.
		$room_type = get_post( $room_type_id );
		if ( is_null( $room_type ) ) {
			throw new \RuntimeException( esc_html__( 'Room type was not found.', 'awebooking' ) );
		}

		return new Room_Type( $room_type );
	}
}
