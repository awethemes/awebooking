<?php

use AweBooking\Booking_Item;

class Booking_Item_Test extends WP_UnitTestCase {
	/**
	 * Set up the test fixture.
	 */
	public function setUp() {
		parent::setUp();
	}

	public function testInsert() {
		$booking_item = new Booking_Item;
		$booking_item['name'] = 'Room Type Luxury';
		$booking_item['booking_id'] = 10;
		$booking_item->save();

		$this->assertInternalType('integer', $booking_item->get_id());
		$dbBookingItem = $this->getBookingItemInDB($booking_item->get_id());

		$this->assertEquals($booking_item->get_id(), $dbBookingItem['booking_item_id']);
		$this->assertEquals($booking_item['name'], $dbBookingItem['booking_item_name']);
		$this->assertEquals($booking_item['type'], $dbBookingItem['booking_item_type']);
		$this->assertEquals($booking_item['booking_id'], $dbBookingItem['booking_id']);
	}

	public function testInsertFailed() {
		$booking_item = new Booking_Item;
		$saved = $booking_item->save();

		$this->assertFalse($saved);
		$this->assertFalse($booking_item->exists());
		$this->assertEmpty($booking_item->get_id());

		$booking_item2 = new Booking_Item;
		$booking_item['name'] = 'Room Type Luxury';
		$saved = $booking_item2->save();

		$this->assertFalse($saved);
		$this->assertFalse($booking_item2->exists());
		$this->assertEmpty($booking_item2->get_id());
	}

	public function testInsertAndUpdate() {
		$booking_item = new Booking_Item;
		$booking_item['name'] = 'Room Type Luxury';
		$booking_item['booking_id'] = 10;
		$booking_item->save();

		// Update Success.
		$booking_item['name'] = 'Luxury';
		$booking_item->save();

		$dbBookingItem = $this->getBookingItemInDB($booking_item->get_id());
		$this->assertEquals($booking_item['name'], $dbBookingItem['booking_item_name']);

		// Update Failed.
		$booking_item['name'] = 'Luxury 1';
		$booking_item['booking_id'] = 200;
		$booking_item->save();

		$dbBookingItem = $this->getBookingItemInDB($booking_item->get_id());
		$this->assertEquals($booking_item['name'], $dbBookingItem['booking_item_name']);
		$this->assertNotEquals($booking_item['booking_id'], $dbBookingItem['booking_id']);
		$this->assertEquals($dbBookingItem['booking_id'], 10);
	}

	public function testDelete() {
		$booking_item = new Booking_Item;
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
