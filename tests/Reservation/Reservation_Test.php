<?php

use AweBooking\Model\Rate;
use AweBooking\Model\Stay;
use AweBooking\Model\Room;
use AweBooking\Model\Guest;
use AweBooking\Model\Room_Type;
use AweBooking\Reservation\Item;
use AweBooking\Reservation\Reservation;
use AweBooking\Model\Source;

class Reservation_Test extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		// Setup data.
		$room_type = new Room_Type;
		$room_type['title']               = 'Single';
		$room_type['status']              = 'publish';
		$room_type['maximum_occupancy']   = 5;
		$room_type['number_adults']       = 4;
		$room_type['number_children']     = 2;
		$room_type['number_infants']      = 2;
		$room_type['calculation_infants'] = true;
		$room_type->save();

		$this->room1 = new Room;
		$this->room1['name']      = 'A101';
		$this->room1['room_type'] = $room_type->get_id();
		$this->room1->save();

		$this->room2 = new Room;
		$this->room2['name']      = 'A102';
		$this->room2['room_type'] = $room_type->get_id();
		$this->room2->save();

		$this->rate = new Rate;
		$this->rate['base_amount'] = 60;
		$this->rate['parent_id']   = $room_type->get_id();
		$this->rate->save();

		$this->room_type = new Room_Type( $room_type->get_id() );
	}

	public function testDataOk() {
		$this->assertTrue( $this->room_type->exists() );
		$this->assertTrue( $this->room1->exists() );
		$this->assertTrue( $this->room2->exists() );
		$this->assertTrue( $this->rate->exists() );
	}

	protected function createReservation() {
		$source = new Source( 'direct', 'Direct' );

		return new Reservation( $source, new Stay( '2017-12-12', '2017-12-22' ) );
	}

	public function testBasicAddRoom() {
		$reservation = $this->createReservation();

		$guest = new Guest( 1, 2, 1 );
		$reservation->add_room( $this->room1, $this->rate, $guest );

		$this->assertCount( 1, $reservation->get_rooms() );
		$this->assertInstanceOf( Item::class, $reservation->get_room( $this->room1 ) );
	}

	/**
	 * @expectedException AweBooking\Reservation\Exceptions\Duplicate_Room_Exception
	 */
	public function _testAddDuplicateRoom() {
		$reservation = $this->createReservation();

		$guest = new Guest( 1, 2, 1 );
		$reservation->add_room( $this->room1, $this->rate, $guest );
		$reservation->add_room( $this->room1, $this->rate, $guest );
	}
}
