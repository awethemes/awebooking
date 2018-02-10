<?php

use AweBooking\Concierge;
use AweBooking\AweBooking;
use AweBooking\Model\Room;
use AweBooking\Model\Room_Type;
use AweBooking\Booking\Booking;
use AweBooking\Booking\Events\Room_State;
use AweBooking\Booking\Items\Line_Item;
use AweBooking\Support\Carbonate;
use AweBooking\Support\Period;

class Test_Lime_Item extends Line_Item {
	public function can_save() {
		return true;
	}
}

class Concierge_Test extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->luxury = $this->setupLuxuryRoomType();
	}

	public function testClearBookingState() {
		$rooms = $this->luxury->get_rooms();

		$booking = new Booking();
		$booking['status'] = 'awebooking-pending';
		$booking->save();

		$period = new Period('2017-06-01', '2017-06-03');
		$created = Concierge::set_booking_state($rooms[0], $period, $booking );
		$this->assertTrue($created);

		$cleaned = Concierge::clear_booking_state( $rooms[0], $period, $booking );
		$this->assertTrue($cleaned);
		$this->assertTrue(Concierge::is_available($rooms[0], $period));

		$this->assertAweBookingTable('awebooking_availability', $rooms[0]->get_id(), 2017, 6, [
			'd1' => Constants::STATE_AVAILABLE,
			'd2' => Constants::STATE_AVAILABLE,
		]);

		$this->assertAweBookingTable('awebooking_booking', $rooms[0]->get_id(), 2017, 6, [
			'd1' => 0,
			'd2' => 0,
		]);

		// False
		$this->assertFalse(Concierge::clear_booking_state( $rooms[0], new Period('2017-06-01', '2017-06-04'), $booking ));
		$this->assertFalse(Concierge::clear_booking_state( $rooms[0], new Period('2017-06-05', '2017-06-06'), $booking ));
	}

	public function testSetRoomAvailability() {
		$rooms = $this->luxury->get_rooms();

		$unavailable = new Room_State($rooms[0], new Period('2017-06-06', '2017-06-07'), Constants::STATE_UNAVAILABLE);
		$unavailable->save();

		$pending = new Room_State($rooms[0], new Period('2017-06-10', '2017-06-15'), Constants::STATE_PENDING);
		$pending->save();

		$unavailable2 = new Room_State($rooms[0], new Period('2017-06-16', '2017-06-18'), Constants::STATE_UNAVAILABLE);
		$unavailable2->save();

		// Test room unavailable then make it available.
		$period1 = new Period( '2017-06-06', '2017-06-08' );
		$this->assertTrue(Concierge::is_unavailable($rooms[0], $period1));
		$this->assertTrue(Concierge::set_availability($rooms[0], $period1, Constants::STATE_AVAILABLE));
		$this->assertTrue(Concierge::is_available($rooms[0], $period1));

		$period2 = new Period( '2017-06-07', '2017-06-09' );
		$this->assertTrue(Concierge::is_available($rooms[0], $period2));
		$this->assertTrue(Concierge::set_availability($rooms[0], $period2, Constants::STATE_UNAVAILABLE));
		$this->assertTrue(Concierge::is_unavailable($rooms[0], $period2));
		$this->assertTrue(Concierge::is_available($rooms[0], new Period( '2017-06-06', '2017-06-07' )));

		$period3 = new Period( '2017-06-09', '2017-06-11' );
		$this->assertTrue(Concierge::has_states($rooms[0], $period3, Constants::STATE_PENDING));
		$this->assertFalse(Concierge::set_availability($rooms[0], $period3, Constants::STATE_AVAILABLE));
		$this->assertTrue(Concierge::has_states($rooms[0], $period3, Constants::STATE_PENDING));

		$period3 = new Period( '2017-06-15', '2017-06-16' );
		$this->assertTrue(Concierge::has_states($rooms[0], $period3, Constants::STATE_AVAILABLE));
		$this->assertFalse(Concierge::set_availability($rooms[0], $period3, Constants::STATE_PENDING));
		$this->assertTrue(Concierge::has_states($rooms[0], $period3, Constants::STATE_AVAILABLE));
	}

	public function testSetBookingState() {
		$rooms = $this->luxury->get_rooms();

		$booking = new Booking();
		$booking['status'] = 'awebooking-pending';
		$booking->save();

		$unavailable = new Room_State($rooms[0], new Period('2017-06-06', '2017-06-07'), Constants::STATE_UNAVAILABLE);
		$unavailable->save();

		$pending = new Room_State($rooms[0], new Period('2017-06-10', '2017-06-15'), Constants::STATE_PENDING);
		$pending->save();

		$this->assertConstantsTable('awebooking_availability', $rooms[0]->get_id(), 2017, 6, [
			'd5'  => 0,
			'd6'  => Constants::STATE_UNAVAILABLE,
			'd10' => Constants::STATE_PENDING,
			'd11' => Constants::STATE_PENDING,
			'd12' => Constants::STATE_PENDING,
			'd13' => Constants::STATE_PENDING,
			'd14' => Constants::STATE_PENDING,
			'd15' => 0
		]);

		$this->assertTrue(Concierge::set_booking_state($rooms[0], new Period('2017-06-01', '2017-06-03'), $booking ));
		$this->assertConstantsTable('awebooking_availability', $rooms[0]->get_id(), 2017, 6, [
			'd1' => Constants::STATE_PENDING,
			'd2' => Constants::STATE_PENDING,
		]);
		$this->assertConstantsTable('awebooking_booking', $rooms[0]->get_id(), 2017, 6, [
			'd1' => $booking->get_id(),
			'd2' => $booking->get_id(),
		]);

		$this->assertFalse(Concierge::set_booking_state($rooms[0], new Period('2017-06-05', '2017-06-07'), $booking ));
		$this->assertFalse(Concierge::set_booking_state($rooms[0], new Period('2017-06-10', '2017-06-12'), $booking ));
	}

	public function testHasStates() {
		$rooms = $this->luxury->get_rooms();

		$unavailable = new Room_State($rooms[0], new Period('2017-06-06', '2017-06-07'), Constants::STATE_UNAVAILABLE);
		$unavailable->save();

		$pending = new Room_State($rooms[0], new Period('2017-06-10', '2017-06-15'), Constants::STATE_PENDING);
		$pending->save();

		$this->assertTrue(Concierge::is_available($rooms[0], new Period( '2017-06-01', '2017-06-06' )));
		$this->assertTrue(Concierge::is_available($rooms[0], new Period( '2017-06-08', '2017-06-10' )));
		$this->assertFalse(Concierge::is_available($rooms[0], new Period( '2017-06-06', '2017-06-07' )));
		$this->assertFalse(Concierge::is_available($rooms[0], new Period( '2017-06-06', '2017-06-08' )));
		$this->assertFalse(Concierge::is_available($rooms[0], new Period( '2017-06-06', '2017-06-09' )));
		$this->assertFalse(Concierge::is_available($rooms[0], new Period( '2017-06-05', '2017-06-09' )));

		$this->assertFalse(Concierge::is_available($rooms[0], new Period( '2017-06-01', '2017-06-15' )));
		$this->assertFalse(Concierge::is_available($rooms[0], new Period( '2017-06-08', '2017-06-13' )));
	}


	protected function setupLuxuryRoomType() {
		$luxury = new Room_Type;
		$luxury['title'] = 'Luxury';
		$luxury['status'] = 'publish';
		$luxury['base_price'] = 150;
		$luxury['number_adults'] = 2;
		$luxury['number_children'] = 2;
		$luxury->save();

		for ( $i = 0; $i < 3; $i++ ) {
			$luxury_room = new Room;
			$luxury_room['name'] = 'Luxury - 10' . $i;
			$luxury_room['room_type'] = $luxury->get_id();
			$luxury_room->save();
		}

		wp_cache_delete( $luxury->get_id(), 'awebooking/rooms_in_room_types' );

		return new Room_Type( $luxury->get_id() );
	}

	protected function assertAweBookingTable($table, $unit_id, $year, $month, $days ) {
		global $wpdb;
		$unit = 'room_id';
		$results = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}{$table}` WHERE `{$unit}` = {$unit_id} AND `year` = '{$year}' AND `month` = '{$month}' LIMIT 1", ARRAY_A);

		if ( empty( $results)) {
			throw new Exception( 'No row found' );
		}

		foreach ( $days as $key => $value) {
			$this->assertEquals($results[$key], $value);
		}
	}

	protected function debugTable() {
		global $wpdb;
		$results = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}awebooking_availability`", ARRAY_A);
		var_dump( $results );
	}
}
