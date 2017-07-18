<?php

use AweBooking\Room_Type;
use AweBooking\Support\Date_Period;
use AweBooking\BAT\Booking_Request;

class Concierge_Test extends WP_UnitTestCase {
	/**
	 * Set up the test fixture.
	 */
	public function setUp() {
		parent::setUp();

		$this->room_type_id1 = $this->factory->post->create([ 'name' => 'Luxury', 'post_type' => 'room_type' ]);
		$this->room_type_id2 = $this->factory->post->create([ 'name' => 'VIP', 'post_type' => 'room_type' ]);
		$this->room_type_id3 = $this->factory->post->create([ 'name' => 'Standard', 'post_type' => 'room_type' ]);

		$this->update_meta( $this->room_type_id1 );
		$this->update_meta( $this->room_type_id2 );
		$this->update_meta( $this->room_type_id3 );

		/*awebooking( 'store.room_type' )->bulk_sync_rooms( $this->room_type_id1, [
			[ 'id' => -1, 'name' => 'Luxury 1' ],
			[ 'id' => -1, 'name' => 'Luxury 2' ],
			[ 'id' => -1, 'name' => 'Luxury 3' ],
		]);*/

		/*awebooking( 'store.room_type' )->bulk_sync_rooms( $this->room_type_id2, [
			[ 'id' => -1, 'name' => 'VIP 1' ],
			[ 'id' => -1, 'name' => 'VIP 2' ],
		]);

		awebooking( 'store.room_type' )->bulk_sync_rooms( $this->room_type_id3, [
			[ 'id' => -1, 'name' => 'Standard 1' ],
			[ 'id' => -1, 'name' => 'Standard 2' ],
			[ 'id' => -1, 'name' => 'Standard 3' ],
			[ 'id' => -1, 'name' => 'Standard 4' ],
		]);*/

		// $this->concierge = awebooking( 'concierge' );
	}

	public function testA() {
	}

	public function __test_check_availability() {
		$date = new Date_Period( '2017-07-10', '2017-07-13', false );
		$request = new Booking_Request( $date, [ 'adults'   => 1, 'children' => 1 ]);

		$results = $this->concierge->check_availability( $request );
		$this->assertNotEmpty( $results );

		foreach ($results as $avai) {
			$this->assertInstanceOf( 'AweBooking\\BAT\\Availability', $avai);
			$this->assertTrue($avai->available());
		}
	}

	protected function update_meta( $id ) {
		add_post_meta( $id, 'base_price', '150' );
		add_post_meta( $id, 'number_adults', '2' );
		add_post_meta( $id, 'number_children', '2' );
		add_post_meta( $id, 'max_adults', '1' );
		add_post_meta( $id, 'max_children', '1' );
		add_post_meta( $id, 'minimum_night', '1' );
	}
}
