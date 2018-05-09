<?php

use AweBooking\Model\Room;
use AweBooking\Model\Common\Timespan;
use AweBooking\Model\Booking\Room_Item;

class Model_Booking_Room_Item_Test extends WP_UnitTestCase {

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

		$room = (new Room())->fill([ 'room_type' => 1, 'name' => '101' ]);
		$room->save();

		abrs_block_room($room, new Timespan('2017-06-12', '2017-06-13'));
		abrs_apply_room_state($room, new Timespan('2017-05-25', '2017-05-26'), 2);

		$booking_item = new Room_Item;
		$booking_item['name']       = 'Luxury';
		$booking_item['room_id']    = $room->get_id();
		$booking_item['booking_id'] = 100;
		$booking_item['check_in']   = '2017-06-01';
		$booking_item['check_out']  = '2017-06-05';
		$booking_item->save();

		$booking_item2 = new Room_Item;
		$booking_item2['name']       = 'Luxury';
		$booking_item2['room_id']    = $room->get_id();
		$booking_item2['booking_id'] = 100;
		$booking_item2['check_in']   = '2017-06-15';
		$booking_item2['check_out']  = '2017-06-21';
		$booking_item2->save();

		$this->assertTrue($booking_item->is_timespan_changeable(new Timespan( '2017-06-01', '2017-06-05' )));
		$this->assertTrue($booking_item->is_timespan_changeable(new Timespan( '2017-06-03', '2017-06-04' )));
		$this->assertTrue($booking_item->is_timespan_changeable(new Timespan( '2017-05-27', '2017-06-10' )));
		$this->assertTrue($booking_item->is_timespan_changeable(new Timespan( '2017-06-01', '2017-06-10' )));
		$this->assertTrue($booking_item->is_timespan_changeable(new Timespan( '2017-05-27', '2017-06-05' )));
		$this->assertTrue($booking_item->is_timespan_changeable(new Timespan( '2017-05-27', '2017-06-03' )));
		$this->assertTrue($booking_item->is_timespan_changeable(new Timespan( '2017-06-04', '2017-06-10' )));
		$this->assertTrue($booking_item->is_timespan_changeable(new Timespan( '2017-05-27', '2017-05-28' )));
		$this->assertTrue($booking_item->is_timespan_changeable(new Timespan( '2017-06-06', '2017-06-10' )));
		$this->assertTrue($booking_item->is_timespan_changeable(new Timespan( '2017-05-27', '2017-06-01' )));
		$this->assertTrue($booking_item->is_timespan_changeable(new Timespan( '2017-06-05', '2017-06-10' )));

		$this->assertFalse($booking_item->is_timespan_changeable(new Timespan( '2017-05-25', '2017-06-07' )));
		$this->assertFalse($booking_item->is_timespan_changeable(new Timespan( '2017-06-01', '2017-06-16' )));
		$this->assertFalse($booking_item->is_timespan_changeable(new Timespan( '2017-06-05', '2017-06-15' )));
		$this->assertFalse($booking_item->is_timespan_changeable(new Timespan( '2017-05-05', '2017-06-04' )));
		$this->assertFalse($booking_item->is_timespan_changeable(new Timespan( '2017-06-03', '2017-06-15' )));
		$this->assertFalse($booking_item->is_timespan_changeable(new Timespan( '2017-05-25', '2017-05-26' )));
		$this->assertFalse($booking_item->is_timespan_changeable(new Timespan( '2017-06-10', '2017-06-15' )));
	}

	protected function debugStateTable() {
		global $wpdb;

		$results = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}awebooking_availability`", ARRAY_A);

		dump( $results );
	}
}
