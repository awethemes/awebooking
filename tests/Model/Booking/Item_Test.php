<?php

use AweBooking\Model\Booking\Item as Booking_Item;

class Model_Booking_Item_Test extends WP_UnitTestCase {
	public function testInsert() {
		$booking_item = new Test_Booking_Item;
		$booking_item['name'] = 'Room Type Luxury';
		$booking_item['booking_id'] = 10;
		$booking_item->save();

		$this->assertInternalType('integer', $booking_item->get_id());
		$dbBookingItem = $this->getBookingItemInDB($booking_item->get_id());

		$this->assertEquals($booking_item->get_id(), $dbBookingItem['booking_item_id']);
		$this->assertEquals($booking_item['name'], $dbBookingItem['booking_item_name']);
		$this->assertEquals($dbBookingItem['booking_item_type'], 'line_items');
		$this->assertEquals($booking_item['booking_id'], $dbBookingItem['booking_id']);
	}

	public function testInsertAndUpdate() {
		$booking_item = new Test_Booking_Item;
		$booking_item['name'] = 'Room Type Luxury';
		$booking_item['booking_id'] = 10;
		$booking_item->save();

		// Update Success.
		$booking_item['name'] = 'Luxury';
		$saved = $booking_item->save();
		$this->assertTrue( $saved );

		$dbBookingItem = $this->getBookingItemInDB($booking_item->get_id());
		$this->assertEquals($booking_item['name'], $dbBookingItem['booking_item_name']);

		// Update with booking ID.
		$booking_item['name'] = 'Luxury 1';
		$booking_item['booking_id'] = 200;
		$saved = $booking_item->save();
		$this->assertTrue( $saved );

		$dbBookingItem = $this->getBookingItemInDB($booking_item->get_id());
		$this->assertEquals($booking_item['name'], $dbBookingItem['booking_item_name']);
		$this->assertEquals($booking_item['booking_id'], $dbBookingItem['booking_id']);
		$this->assertEquals($dbBookingItem['booking_id'], 200);
	}

	public function testDelete() {
		$booking_item = new Test_Booking_Item;
		$booking_item['name'] = 'Room Type Luxury';
		$booking_item['booking_id'] = 10;
		$booking_item->save();

		$booking_item->delete();
		$this->assertFalse($booking_item->exists());
		$this->assertNull($this->getBookingItemInDB($booking_item->get_id()));
	}

	protected function getBookingItemInDB( $id ) {
		global $wpdb;

		return $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}awebooking_booking_items` WHERE `booking_item_id` = '%d' LIMIT 1", $id ),
			ARRAY_A
		);
	}
}

class Test_Booking_Item extends Booking_Item {
	public function get_type() {
		return 'line_items';
	}
}
