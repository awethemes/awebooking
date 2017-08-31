<?php

use AweBooking\Hotel\Room;

class Room_Test extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();
	}

	public function testInsertAndGet() {
		$booking_item = new Room;
		$booking_item['name'] = 'Luxury - 1010';
		$booking_item['room_type_id'] = 10;
		$booking_item->save();

		$this->assertInternalType('integer', $booking_item->get_id());
		$dbBookingItem = $this->getItemInDB($booking_item->get_id());

		$this->assertEquals($booking_item->get_id(), $dbBookingItem['id']);
		$this->assertEquals($booking_item['name'], $dbBookingItem['name']);
		$this->assertEquals($booking_item['room_type_id'], $dbBookingItem['room_type']);
	}

	public function testInsertFailed() {
		$booking_item = new Room;
		$saved = $booking_item->save();

		$this->assertFalse($saved);
		$this->assertFalse($booking_item->exists());
		$this->assertEmpty($booking_item->get_id());

		$booking_item2 = new Room;
		$booking_item['name'] = 'Luxury - 101';
		$saved = $booking_item2->save();

		$this->assertFalse($saved);
		$this->assertFalse($booking_item2->exists());
		$this->assertEmpty($booking_item2->get_id());
	}

	public function testInsertAndUpdate() {
		$booking_item = new Room;
		$booking_item['name'] = 'Luxury - 101';
		$booking_item['room_type_id'] = 10;
		$booking_item->save();

		// Update Success.
		$booking_item['name'] = 'Luxury - 102';
		$booking_item->save();

		$dbBookingItem = $this->getItemInDB($booking_item->get_id());
		$this->assertEquals($booking_item['name'], $dbBookingItem['name']);

		// Update with booking ID.
		$booking_item['name'] = 'Luxury 103';
		$booking_item['room_type_id'] = 200;
		$booking_item->save();

		$dbBookingItem = $this->getItemInDB($booking_item->get_id());
		$this->assertEquals($booking_item['name'], $dbBookingItem['name']);
		$this->assertEquals($booking_item['room_type_id'], $dbBookingItem['room_type']);
		$this->assertEquals($dbBookingItem['room_type'], 200);
	}

	public function testDelete() {
		$booking_item = new Room;
		$booking_item['name'] = 'Luxury - 101';
		$booking_item['room_type_id'] = 10;

		$booking_item->save();
		$booking_item->delete();

		$this->assertFalse($booking_item->exists());
		$this->assertNull($this->getItemInDB($booking_item->get_id()));
	}

	protected function getItemInDB( $id ) {
		global $wpdb;

		return $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}awebooking_rooms` WHERE `id` = '%d' LIMIT 1", $id ),
			ARRAY_A
		);
	}
}
