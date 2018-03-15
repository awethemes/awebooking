<?php

use AweBooking\Model\Common\Timespan;
use AweBooking\Model\Rate;
use AweBooking\Model\Room;
use \AweBooking\Model\Common\Guest_Counts;
use AweBooking\Reservation\Item;

class Reservation_Item_Test extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();
	}

	public function testConstructor() {
		$room  = new Room;
		$rate  = new Rate;
		$timespan  = new Timespan( '2017-12-12', '2017-12-13' );
		$guest = new Guest( 1, 1, 1 );

		$item = new Item( $room, $rate, $timespan, $guest );
		$this->assertInstanceOf( Room::class, $item->get_room() );
		$this->assertInstanceOf( Rate::class, $item->get_rate() );
		$this->assertInstanceOf( Timespan::class, $item->get_timespan() );
		$this->assertInstanceOf( Guest::class, $item->get_guest() );

		$this->assertSame( $room, $item->get_room() );
		$this->assertSame( $rate, $item->get_rate() );
		$this->assertSame( $timespan, $item->get_timespan() );
		$this->assertSame( $guest, $item->get_guest() );
	}
}
