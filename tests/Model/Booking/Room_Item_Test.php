<?php

use AweBooking\Model\Room;
use AweBooking\Model\Common\Timespan;
use AweBooking\Model\Booking\Room_Item;

class Model_Booking_Room_Item_Test extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$room = (new Room())->fill([ 'room_type' => 1, 'name' => '101' ]);
		$room->save();

		$this->room = $room;
	}

	public function testSetTimespan() {
		$booking_item = new Room_Item;
		$booking_item['room_id'] = $this->room->get_id();
		$booking_item->set_timespan( abrs_timespan( '2018-05-10', '2018-05-12' ) );
		$booking_item->save();

		$this->assertStateData($this->room->get_id(), 2018, 5, [
			'd10' => 2,
			'd11' => 2,
		]);

		// False.
		$this->assertFalse( $booking_item->set_timespan( abrs_timespan( '2018-05-11', '2018-05-15' ) ) );

		$changed = $booking_item->update_timespan( abrs_timespan( '2018-05-11', '2018-05-15' ) );
		$this->assertTrue( $changed );

		$this->assertStateData($this->room->get_id(), 2018, 5, [
			'd10' => 0,
			'd11' => 2,
			'd12' => 2,
			'd13' => 2,
			'd14' => 2,
		]);
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

		abrs_block_room($this->room, new Timespan('2017-06-12', '2017-06-13'));
		abrs_apply_room_state($this->room, new Timespan('2017-05-25', '2017-05-26'), 2);

		$booking_item = new Room_Item;
		$booking_item['name']       = 'Luxury';
		$booking_item['room_id']    = $this->room->get_id();
		$booking_item['booking_id'] = 100;
		$booking_item['check_in']   = '2017-06-01';
		$booking_item['check_out']  = '2017-06-05';
		$booking_item->save();

		$this->assertTrue($booking_item->timespan_changeable(new Timespan( '2017-06-01', '2017-06-05' )));
		$this->assertTrue($booking_item->timespan_changeable(new Timespan( '2017-06-03', '2017-06-04' )));
		$this->assertTrue($booking_item->timespan_changeable(new Timespan( '2017-05-27', '2017-06-10' )));
		$this->assertTrue($booking_item->timespan_changeable(new Timespan( '2017-06-01', '2017-06-10' )));
		$this->assertTrue($booking_item->timespan_changeable(new Timespan( '2017-05-27', '2017-06-05' )));
		$this->assertTrue($booking_item->timespan_changeable(new Timespan( '2017-05-27', '2017-06-03' )));
		$this->assertTrue($booking_item->timespan_changeable(new Timespan( '2017-06-04', '2017-06-10' )));
		$this->assertTrue($booking_item->timespan_changeable(new Timespan( '2017-05-27', '2017-05-28' )));
		$this->assertTrue($booking_item->timespan_changeable(new Timespan( '2017-06-06', '2017-06-10' )));
		$this->assertTrue($booking_item->timespan_changeable(new Timespan( '2017-05-27', '2017-06-01' )));
		$this->assertTrue($booking_item->timespan_changeable(new Timespan( '2017-06-05', '2017-06-10' )));

		$this->assertFalse($booking_item->timespan_changeable(new Timespan( '2017-05-25', '2017-06-07' )));
		$this->assertFalse($booking_item->timespan_changeable(new Timespan( '2017-06-01', '2017-06-16' )));
		$this->assertFalse($booking_item->timespan_changeable(new Timespan( '2017-06-05', '2017-06-15' )));
		$this->assertFalse($booking_item->timespan_changeable(new Timespan( '2017-05-05', '2017-06-04' )));
		$this->assertFalse($booking_item->timespan_changeable(new Timespan( '2017-06-03', '2017-06-15' )));
		$this->assertFalse($booking_item->timespan_changeable(new Timespan( '2017-05-25', '2017-05-26' )));
		$this->assertFalse($booking_item->timespan_changeable(new Timespan( '2017-06-10', '2017-06-15' )));
	}

	protected function assertStateData( $unit_id, $year, $month, $days ) {
		global $wpdb;

		$results = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}awebooking_availability` WHERE `room_id` = {$unit_id} AND `year` = '{$year}' AND `month` = '{$month}' LIMIT 1", ARRAY_A);

		foreach ( $days as $key => $value) {
			$this->assertEquals($results[$key], $value);
		}
	}

	protected function debugStateTable() {
		global $wpdb;

		$results = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}awebooking_availability`", ARRAY_A);

		dump( $results );
	}
}
