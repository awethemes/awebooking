<?php

use AweBooking\Hotel\Room;
use AweBooking\Hotel\Room_Type;
use AweBooking\Hotel\Room_State;
use AweBooking\Booking\Items\Line_Item;
use AweBooking\Support\Carbonate;
use AweBooking\Support\Date_Period;

class Test_Lime_Item extends Line_Item {
	public function can_save() {
		return true;
	}
}

class Concierge_Test extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->concierge = awebooking()->make( 'concierge' );
		$this->luxury = $this->setupLuxuryRoomType();
	}

	public function testHasEmptyRoom() {
		$rooms = $this->luxury->get_rooms();

		$state = new Room_State($rooms[0], Carbonate::create_date('2017-06-06'), Carbonate::create_date('2017-06-07'), Room_State::UNAVAILABLE);
		$state->save();

		$state2 = new Room_State($rooms[0], Carbonate::create_date('2017-06-10'), Carbonate::create_date('2017-06-15'), Room_State::UNAVAILABLE);
		$state2->save();

		$this->assertTrue($rooms[0]->is_free(new Date_Period( '2017-06-01', '2017-06-06' )));
		$this->assertTrue($rooms[0]->is_free(new Date_Period( '2017-06-08', '2017-06-10' )));
		$this->assertFalse($rooms[0]->is_free(new Date_Period( '2017-06-06', '2017-06-07' )));
		$this->assertFalse($rooms[0]->is_free(new Date_Period( '2017-06-06', '2017-06-08' )));
		$this->assertFalse($rooms[0]->is_free(new Date_Period( '2017-06-06', '2017-06-09' )));
		$this->assertFalse($rooms[0]->is_free(new Date_Period( '2017-06-05', '2017-06-09' )));

		$this->assertFalse($rooms[0]->is_free(new Date_Period( '2017-06-01', '2017-06-15' )));
		$this->assertFalse($rooms[0]->is_free(new Date_Period( '2017-06-08', '2017-06-13' )));
	}

	public function testChangeable() {
		// 1. Same
		// --------------|===============|--------------
		// --------------|===============|--------------
		//
		// 2. Inside
		// --------------|===============|--------------
		// ------------------|========|-----------------
		//
		// 3. Wrap
		// --------------|===============|--------------
		// -----------|=====================|----------
		//
		// 4. Same startpoint
		// --------------|===============|--------------
		// --------------|=====================|--------
		//
		// 5. Same endpoint.
		// --------------|===============|--------------
		// --------|=====================|--------------
		//
		// 6. Diff
		// --------------|===============|--------------
		// --------|==============|---------------------
		//
		// 7. Outside left
		// --------------|===============|--------------
		// ---|=====|-----------------------------------
		//
		// 8. Outside right
		// --------------|===============|--------------
		// -----------------------------------|=====|---
		//

		$rooms = $this->luxury->get_rooms();

		$state = new Room_State($rooms[0], Carbonate::create_date('2017-06-12'), Carbonate::create_date('2017-06-13'), Room_State::UNAVAILABLE);
		$state->save();

		$state2 = new Room_State($rooms[0], Carbonate::create_date('2017-05-25'), Carbonate::create_date('2017-05-26'), Room_State::BOOKED);
		$state2->save();

		$booking_item = new Test_Lime_Item;
		$booking_item['name']       = 'Luxury';
		$booking_item['room_id']    = $rooms[0]->get_id();
		$booking_item['booking_id'] = 100;
		$booking_item['check_in']   = '2017-06-01';
		$booking_item['check_out']  = '2017-06-05';
		$booking_item->save();

		$booking_item2 = new Test_Lime_Item;
		$booking_item2['name']       = 'Luxury';
		$booking_item2['room_id']    = $rooms[0]->get_id();
		$booking_item2['booking_id'] = 100;
		$booking_item2['check_in']   = '2017-06-10';
		$booking_item2['check_out']  = '2017-06-15';
		$booking_item2->save();

		$this->assertTrue($booking_item->is_changeable(new Date_Period( '2017-06-01', '2017-06-05' )));
		$this->assertTrue($booking_item->is_changeable(new Date_Period( '2017-06-03', '2017-06-04' )));
		$this->assertTrue($booking_item->is_changeable(new Date_Period( '2017-05-27', '2017-06-10' )));
		$this->assertTrue($booking_item->is_changeable(new Date_Period( '2017-06-01', '2017-06-10' )));
		$this->assertTrue($booking_item->is_changeable(new Date_Period( '2017-05-27', '2017-06-05' )));
		$this->assertTrue($booking_item->is_changeable(new Date_Period( '2017-05-27', '2017-06-03' )));
		$this->assertTrue($booking_item->is_changeable(new Date_Period( '2017-06-04', '2017-06-10' )));
		$this->assertTrue($booking_item->is_changeable(new Date_Period( '2017-05-27', '2017-05-28' )));
		$this->assertTrue($booking_item->is_changeable(new Date_Period( '2017-06-06', '2017-06-10' )));
		$this->assertTrue($booking_item->is_changeable(new Date_Period( '2017-05-27', '2017-06-01' )));
		$this->assertTrue($booking_item->is_changeable(new Date_Period( '2017-06-05', '2017-06-10' )));

		$this->assertFalse($booking_item->is_changeable(new Date_Period( '2017-05-25', '2017-06-07' )));
		$this->assertFalse($booking_item->is_changeable(new Date_Period( '2017-06-01', '2017-06-11' )));
		$this->assertFalse($booking_item->is_changeable(new Date_Period( '2017-06-05', '2017-06-15' )));
		$this->assertFalse($booking_item->is_changeable(new Date_Period( '2017-05-05', '2017-06-04' )));
		$this->assertFalse($booking_item->is_changeable(new Date_Period( '2017-06-03', '2017-06-15' )));
		$this->assertFalse($booking_item->is_changeable(new Date_Period( '2017-05-25', '2017-05-26' )));
		$this->assertFalse($booking_item->is_changeable(new Date_Period( '2017-06-10', '2017-06-15' )));
	}

	protected function debugTable() {
		global $wpdb;
		$results = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}awebooking_availability`", ARRAY_A);
		var_dump( $results );
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
			$luxury_room['room_type_id'] = $luxury->get_id();
			$luxury_room->save();
		}

		wp_cache_delete( $luxury->get_id(), 'awebooking/rooms_in_room_types' );

		return new Room_Type( $luxury->get_id() );
	}
}
