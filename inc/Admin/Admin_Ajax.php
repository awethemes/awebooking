<?php

namespace AweBooking\Admin;

use Carbon\Carbon;
use AweBooking\Room;
use AweBooking\Room_Type;
use AweBooking\Room_State;
use AweBooking\BAT\Calendar;
use AweBooking\BAT\Factory;
use AweBooking\Support\Date_Period;
use AweBooking\Admin\Calendar\Yearly_Calendar;
use Roomify\Bat\Event\Event;

class Admin_Ajax {

	public function __construct() {
		$ajax_hooks = [
			'set_event' => 'set_event',
			'get_yearly_calendar' => 'get_yearly_calendar',
			'set_pricing' => 'set_pricing',
			'check_availability' => 'check_availability',
			'add_booking_item' => 'add_booking_item',
		];

		foreach ( $ajax_hooks as $id => $action ) {
			add_action( 'wp_ajax_awebooking/' . $id, [ $this, $action ] );
		}
	}

	/**
	 * //
	 *
	 * @return string
	 */
	public function check_availability() {
		try {
			$booking_request = Factory::create_booking_request( $_REQUEST, false );

			if ( isset( $_REQUEST['exclude_rooms'] ) ) {
				$booking_request->set_request( 'exclude_rooms', (array) $_REQUEST['exclude_rooms'] );
			}

			$response = awebooking( 'concierge' )->check_availability( $booking_request );
		} catch ( \Exception $e ) {
			return wp_send_json_error( [ 'message' => $e->getMessage() ] );
		}

		if ( empty( $response ) ) {
			return wp_send_json_error( [ 'message' => 'No room available' ] );
		}

		$return = [];
		foreach ( $response as $availability ) {
			if ( $availability->unavailable() ) {
				continue;
			}

			$return[] = $availability->to_array();
		}

		return wp_send_json_success( $return );
	}













	public function add_booking_item() {
		$concierge = awebooking( 'concierge' );

		try {
			$room_type = Factory::create_room_from_request();

			$booking_request = Factory::create_booking_request( $_REQUEST, false );

			$availability = $concierge->check_room_type_availability( $room_type, $booking_request );

		} catch ( \Exception $e ) {
			return wp_send_json_error( [ 'message' => $e->getMessage() ] );
		}

		if ( $availability->unavailable() ) {
			return wp_send_json_error( [ 'message' => esc_html__( 'No room available', 'awebooking' ) ] );
		}

		// Get booking.
		$booking = get_post( $_REQUEST['booking_id'] );
		if ( ! $booking ) {
			return wp_send_json_error();
		}

		// Get last room from availability.
		$rooms = $availability->get_rooms();
		$room = end( $rooms );

		$response = [
			'room_id'      => $room->get_id(),
			'room_type_id' => $room_type->get_id(),

			'extra_services' => [],

			'adults' => $availability->get_adults(),
			'children' => $availability->get_children(),

			'check_in' => $availability->get_check_in()->toDateString(),
			'check_out' => $availability->get_check_out()->toDateString(),
		];

		$response['price'] = (string) $availability->get_price();
		$response['total_price'] = (string) $availability->get_total_price();

		$response['room'] = $room->to_array();
		$response['room_type'] = $room_type->to_array();
		$response['nights'] = (string) $availability->get_nights();

		return wp_send_json_success( $response );
	}


	public function set_pricing() {
		$start = $_REQUEST['start'];
		$end = $_REQUEST['end'];

		$start_day = abkng_create_datetime( $start )->startOfDay();
		$end_day = abkng_create_datetime( $end )->startOfDay()->subMinute();

		$rate = new Rate( $_REQUEST['rate_id'] );
		$pricing = new Room_State( $rate, $start_day, $end_day, $_REQUEST['price'] );

		$pricing->save();

		wp_send_json_success();
	}

	public function get_yearly_calendar() {
		$room = new Room( $_REQUEST['room'] );
		$calendar = new Yearly_Calendar( $_REQUEST['year'], $room );
		$calendar->display();
		exit;
	}

	public function set_event() {
		$start = $_REQUEST['start'];
		$end = $_REQUEST['end'];

		$date_period = new Date_Period( $start, $end, false );
		$room = new Room( $_REQUEST['room_id'] );

		awebooking( 'concierge' )->set_room_state( $room, $date_period, $_REQUEST['state'] );

		wp_send_json_success();
	}
}
