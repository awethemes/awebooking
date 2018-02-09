<?php

use AweBooking\Constants;
use AweBooking\Model\Room;
use AweBooking\Model\Stay;
use AweBooking\Model\Booking_Room_Item;

use AweBooking\Calendar\Calendar;
use AweBooking\Calendar\Resource\Resource;
use AweBooking\Calendar\Event\State_Event;
use AweBooking\Calendar\Provider\State_Provider;

class Model_Booking_Room_Item_Test extends WP_UnitTestCase {
	public function testChangeable() {
		$room = ( new Room )->fill([ 'room_type' => 1 ]);
		$room->save();

		$resource = new Resource( $room->get_id() );
		$calendar = new Calendar( $resource, new State_Provider( $resource) );

		// Fake events.
		$calendar->store( new State_Event( $resource, '2017-05-15', '2017-05-21', Constants::STATE_PENDING ) );
		$calendar->store( new State_Event( $resource, '2017-05-25', '2017-05-26', Constants::STATE_PENDING ) );
		$calendar->store( new State_Event( $resource, '2017-06-12', '2017-06-13', Constants::STATE_UNAVAILABLE ) );

		$booking_item = new Booking_Room_Item;
		$booking_item['name']       = 'Luxury';
		$booking_item['room_id']    = $room->get_id();
		$booking_item['booking_id'] = 100;
		$booking_item['check_in']   = '2017-06-01';
		$booking_item['check_out']  = '2017-06-05';

		$booking_item->save();
		$calendar->store( new State_Event( $resource, '2017-06-01', '2017-06-05', Constants::STATE_BOOKED ) );

		// Check booking:
		// 			"2017-06-01", "2017-06-05"
		// Two unavaible period:
		// 			'2017-05-25', '2017-05-26'
		// 			'2017-06-12', '2017-06-13'
		// 			'2017-06-15', '2017-06-21'

		// 1. Same
		// --------------|===============|--------------
		// --------------|===============|--------------
		$this->assertTrue($booking_item->is_changeable(new Stay( '2017-06-01', '2017-06-05' )));

		//
		// 2. Inside
		// --------------|===============|--------------
		// ------------------|========|-----------------
		$this->assertTrue($booking_item->is_changeable(new Stay( '2017-06-02', '2017-06-03' )));
		$this->assertTrue($booking_item->is_changeable(new Stay( '2017-06-02', '2017-06-04' )));
		$this->assertTrue($booking_item->is_changeable(new Stay( '2017-06-03', '2017-06-04' )));

		// 3. Wrap
		// --------------|===============|--------------
		// -----------|=====================|----------
		$this->assertTrue($booking_item->is_changeable(new Stay( '2017-05-27', '2017-06-10' )));
		$this->assertFalse($booking_item->is_changeable(new Stay( '2017-05-25', '2017-06-07' )));

		// 4. Same startpoint
		// --------------|===============|--------------
		// -----|========|------------------------------
		// --------------|=====================|--------
		$this->assertTrue($booking_item->is_changeable(new Stay( '2017-05-27', '2017-06-01' )));
		$this->assertTrue($booking_item->is_changeable(new Stay( '2017-06-01', '2017-06-10' )));
		$this->assertFalse($booking_item->is_changeable(new Stay( '2017-06-01', '2017-06-16' )));

		// 5. Same endpoint.
		// --------------|===============|--------------
		// ------------------------------|=========|----
		// --------|=====================|--------------
		$this->assertTrue($booking_item->is_changeable(new Stay( '2017-05-27', '2017-06-05' )));
		$this->assertTrue($booking_item->is_changeable(new Stay( '2017-06-05', '2017-06-10' )));
		$this->assertFalse($booking_item->is_changeable(new Stay( '2017-06-05', '2017-06-15' )));

		// 6. Diff
		// --------------|===============|--------------
		// --------|==============|---------------------
		// -------------------|==============|----------
		$this->assertTrue($booking_item->is_changeable(new Stay( '2017-06-04', '2017-06-10' )));
		$this->assertTrue($booking_item->is_changeable(new Stay( '2017-05-27', '2017-06-03' )));
		$this->assertFalse($booking_item->is_changeable(new Stay( '2017-05-05', '2017-06-04' )));
		$this->assertFalse($booking_item->is_changeable(new Stay( '2017-06-03', '2017-06-15' )));

		// 7. Outside left
		// --------------|===============|--------------
		// ---|=====|-----------------------------------
		$this->assertTrue($booking_item->is_changeable(new Stay( '2017-05-27', '2017-05-28' )));
		$this->assertFalse($booking_item->is_changeable(new Stay( '2017-05-15', '2017-05-21')));
		$this->assertFalse($booking_item->is_changeable(new Stay( '2017-05-25', '2017-05-26' )));

		// 8. Outside right
		// --------------|===============|--------------
		// -----------------------------------|=====|---
		$this->assertTrue($booking_item->is_changeable(new Stay( '2017-06-06', '2017-06-10' )));
		$this->assertTrue($booking_item->is_changeable(new Stay( '2017-06-21', '2017-06-22' )));
		$this->assertFalse($booking_item->is_changeable(new Stay( '2017-06-10', '2017-06-15' )));
		$this->assertFalse($booking_item->is_changeable(new Stay( '2017-06-12', '2017-06-13' )));
	}

	protected function debugTable() {
		global $wpdb;

		return $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}awebooking_availability`", ARRAY_A);
	}
}
