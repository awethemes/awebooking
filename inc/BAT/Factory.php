<?php
namespace AweBooking\BAT;

use RuntimeException;
use AweBooking\Room_Type;
use AweBooking\Room_State;
use AweBooking\Support\Date_Period;

class Factory {
	/**
	 * Create availability calendar.
	 *
	 * @param  array   $rooms         Array of rooms.
	 * @param  integer $default_state Default availability state.
	 * @return Calendar
	 */
	public static function create_availability_calendar( array $rooms, $default_state = Room_State::AVAILABLE ) {
		return new Calendar( $rooms, awebooking( 'store.availability' ), $default_state );
	}

	/**
	 * Create booking calendar.
	 *
	 * @param  array   $rooms      Array of rooms.
	 * @param  integer $booking_id Booking ID.
	 * @return Calendar
	 */
	public static function create_booking_calendar( array $rooms, $booking_id = 0 ) {
		return new Calendar( $rooms, awebooking( 'store.booking' ), $booking_id );
	}

	/**
	 * Create pricing calendar.
	 *
	 * @param  array   $rates         Array of rates.
	 * @param  integer $default_price Default rate price.
	 * @return Calendar
	 */
	public static function create_pricing_calendar( array $rates, $default_price = 0 ) {
		return new Calendar( $rooms, awebooking( 'store.pricing' ), $default_price );
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
	public static function create_booking_request( array $request = null, $strict = true ) {
		if ( is_null( $request ) ) {
			$request = $_REQUEST;
		}

		$start_date = isset( $request['start-date'] ) ? $request['start-date'] :
			( isset( $request['start_date'] ) ? $request['start_date'] : null );

		$end_date = isset( $request['end-date'] ) ? $request['end-date'] :
			( isset( $request['end_date'] ) ? $request['end_date'] : null );

		if ( empty( $start_date ) || empty( $start_date ) ) {
			throw new RuntimeException( esc_html__( 'Start date and end date must be shown.', 'awebooking' ) );
		}

		$period = new Date_Period(
			sanitize_text_field( wp_unslash( $start_date ) ),
			sanitize_text_field( wp_unslash( $end_date ) ),
			$strict
		);

		// Take accept requests.
		$booking_requests = [];
		$accept_requests  = [ 'adults', 'children', 'location', 'room-type' ];

		// Loop through the accept_requests and build booking_requests.
		foreach ( $accept_requests as $id ) {
			$booking_requests[ $id ] = isset( $request[ $id ] ) ? sanitize_text_field( wp_unslash( $request[ $id ] ) ) : null;
		}

		// Validate some request.
		if ( ! is_null( $booking_requests['adults'] ) ) {
			$booking_requests['adults'] = absint( $booking_requests['adults'] );
		}

		if ( ! is_null( $booking_requests['children'] ) ) {
			$booking_requests['children'] = absint( $booking_requests['children'] );
		}

		return new Booking_Request( $period, $booking_requests );
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
			throw new RuntimeException( esc_html__( 'Room type was not found.', 'awebooking' ) );
		}

		return new Room_Type( $room_type );
	}
}
