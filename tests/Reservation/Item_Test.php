<?php

use AweBooking\Model\Stay;
use AweBooking\Model\Rate;
use AweBooking\Model\Room;
use AweBooking\Model\Guest;
use AweBooking\Reservation\Item;

class Reservation_Item_Test extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();
	}

	public function testConstructor() {
		$room  = new Room;
		$rate  = new Rate;
		$stay  = new Stay( '2017-12-12', '2017-12-13' );
		$guest = new Guest( 1, 1, 1 );

		$item = new Item( $room, $rate, $stay, $guest );
		$this->assertInstanceOf( Room::class, $item->get_room() );
		$this->assertInstanceOf( Rate::class, $item->get_rate() );
		$this->assertInstanceOf( Stay::class, $item->get_stay() );
		$this->assertInstanceOf( Guest::class, $item->get_guest() );

		$this->assertSame( $room, $item->get_room() );
		$this->assertSame( $rate, $item->get_rate() );
		$this->assertSame( $stay, $item->get_stay() );
		$this->assertSame( $guest, $item->get_guest() );
	}
}
