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
			'check_availability' => 'check_availability',
			'add_booking_item' => 'add_booking_item',
		];

		foreach ( $ajax_hooks as $id => $action ) {
			add_action( 'wp_ajax_awebooking/' . $id, [ $this, $action ] );
		}
	}


	public function get_yearly_calendar() {
		$room = new Room( absint( $_REQUEST['room'] ) );

		if ( $room->exists() ) {
			$calendar = new Yearly_Calendar( absint( $_REQUEST['year'] ), $room );
			$calendar->display();
		}

		exit;
	}

	public function set_event() {
		if ( empty( $_REQUEST['start'] ) || empty( $_REQUEST['start'] ) || ! isset( $_REQUEST['state'] ) ) {
			return wp_send_json_error();
		}

		$start = sanitize_text_field( wp_unslash( $_REQUEST['start'] ) );
		$end = sanitize_text_field( wp_unslash( $_REQUEST['end'] ) );

		try {
			$date_period = new Date_Period( $start, $end, false );
			$room = new Room( absint( $_REQUEST['room_id'] ) );

			if ( $room->exists() ) {
				awebooking( 'concierge' )->set_room_state( $room, $date_period, absint( $_REQUEST['state'] ) );
			}

			return wp_send_json_success();
		} catch ( \Exception $e ) {
			return wp_send_json_error();
		}
	}
}
