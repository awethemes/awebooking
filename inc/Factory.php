<?php
namespace AweBooking;

use AweBooking\Hotel\Room;
use AweBooking\Hotel\Room_Type;
use AweBooking\Booking\Booking;
use AweBooking\Booking\Calendar;
use AweBooking\Booking\Request;
use AweBooking\Booking\Items\Line_Item;
use AweBooking\Booking\Items\Service_Item;
use AweBooking\Booking\Items\Booking_Item;
use AweBooking\Support\Period;

/**
 * Simple Factory Pattern
 *
 * Create all things related to AweBooking.
 */
class Factory {
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
	 * Create availability calendar.
	 *
	 * @param  array   $rooms         Array of rooms.
	 * @param  integer $default_state Default availability state.
	 * @return Calendar
	 */
	public static function create_availability_calendar( array $rooms, $default_state = AweBooking::STATE_AVAILABLE ) {
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
	 * Gets room unit by ID.
	 *
	 * @param  int $room_unit Room unit ID.
	 * @return AweBooking\Hotel\Room
	 */
	public static function get_room_unit( $room_unit ) {
		return new Room( $room_unit );
	}

	/**
	 * Gets room type by ID.
	 *
	 * @param  int $room_type Room type ID or instance.
	 * @return AweBooking\Hotel\Room_Type
	 */
	public static function get_room_type( $room_type ) {
		return new Room_Type( $room_type );
	}

	/**
	 * Gets booking by ID.
	 *
	 * @param  int $booking_id Booking ID.
	 * @return AweBooking\Booking\Booking
	 */
	public static function get_booking( $booking_id ) {
		return new Booking( $booking_id );
	}

	/**
	 * Gets booking item instance by ID.
	 *
	 * @param  int $item_id Booking item ID.
	 * @return mixed|false|null
	 */
	public static function get_booking_item( $item_id ) {
		if ( is_numeric( $item_id ) ) {
			global $wpdb;
			$item_data = $wpdb->get_row( $wpdb->prepare( "SELECT `booking_item_type` FROM `{$wpdb->prefix}awebooking_booking_items` WHERE `booking_item_id` = %d LIMIT 1", $item_id ), ARRAY_A );

			$id        = $item_id;
			$item_type = isset( $item_data['booking_item_type'] ) ? $item_data['booking_item_type'] : false;
		} elseif ( $item_id instanceof Booking_Item ) {
			$id        = $item_id->get_id();
			$item_type = $item_id->get_type();
		} elseif ( is_array( $item_id ) && ! empty( $item_id['booking_item_type'] ) ) {
			$id        = $item_id['booking_item_id'];
			$item_type = $item_id['booking_item_type'];
		} else {
			$id        = false;
			$item_type = false;
		}

		// Found invalid ID or type, just return.
		if ( ! $id || ! $item_type ) {
			return;
		}

		// Resolve booking item class by type.
		$classname = static::resolve_booking_item_class( $item_type );
		if ( ! $classname || ! class_exists( $classname ) ) {
			return false;
		}

		try {
			return new $classname( $id );
		} catch ( \Exception $e ) {
			return false;
		}
	}

	/**
	 * Resolve booking item class by type.
	 *
	 * @param  string $type Booking item type.
	 * @return string|null
	 */
	protected static function resolve_booking_item_class( $type ) {
		$maps = apply_filters( 'awebooking/booking_item_class_maps', [
			'line_item'    => Line_Item::class,
			'service_item' => Service_Item::class,
		]);

		if ( array_key_exists( $type, $maps ) ) {
			return $maps[ $type ];
		}
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
