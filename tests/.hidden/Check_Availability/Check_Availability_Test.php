<?php

use AweBooking\Hotel\Room_Type;
use AweBooking\Concierge;
use AweBooking\Factory;
use AweBooking\Booking\Request;
use AweBooking\Support\Period;

class Check_Availability_Test extends WP_UnitTestCase {
	/**
	 * Set up the test fixture.
	 */
	public function setUp() {
		parent::setUp();
		awebooking( 'setting' )->set( 'infants_bookable.enable', true );

		$luxury = new Room_Type;
		$luxury['title'] = 'Luxury';
		$luxury['status'] = 'publish';
		$luxury['base_price'] = 100;

		$luxury['number_adults'] = 3;
		$luxury['max_adults'] = 3;

		$luxury['number_children'] = 2;
		$luxury['max_children'] = 1;

		$luxury['max_infants'] = 3;
		$luxury['number_infants'] = 2;

		$luxury['minimum_night'] = 2;
		$luxury->save();
		$this->luxury = $luxury;


		$deluxe = new Room_Type;
		$deluxe['title'] = 'deluxe';
		$deluxe['status'] = 'publish';
		$deluxe['base_price'] = 100;
		$deluxe['number_adults'] = 2;
		$deluxe['max_adults'] = 2;

		$deluxe['number_children'] = 3;
		$deluxe['max_children'] = 2;

		$deluxe['number_infants'] = 2;
		$deluxe['max_infants'] = 1;

		$deluxe['minimum_night'] = 3;
		$deluxe->save();
		$this->deluxe = $deluxe;

		$vip = new Room_Type;
		$vip['title'] = 'vip';
		$vip['status'] = 'publish';
		$vip['base_price'] = 100;

		$vip['number_adults'] = 4;
		$vip['max_adults'] = 3;

		$vip['max_children'] = 1;
		$vip['number_children'] = 1;

		$vip['number_infants'] = 2;
		$vip['max_infants'] = 2;

		$vip['minimum_night'] = 4;
		$vip->save();
		$this->vip = $vip;
	}

	/**
	 * Test room type
	 */
	public function test_room_type() {
		$query = Room_Type::query( [
			'post_type'   => AweBooking::ROOM_TYPE,
		] );

		$this->assertCount( 3, $query->posts );
	}

	/**
	 * Test not room type
	 */
	public function test_not_room_type() {
		$query = Room_Type::query( [
			'post_type'   => 'post',
		] );

		$this->assertCount( 0, $query->posts );
	}

	/**
	 * @dataProvider get_test_query
	 */
	public function test_query( $query, $count ) {
		$query = Room_Type::query( $query );

		$this->assertCount( $count, $query->posts );
	}

	public function get_test_query() {
		return [
			// Test adults.
			[ [	'booking_adults'   => 0 ], 3 ],
			[ [	'booking_adults'   => 1 ], 3 ],
			[ [	'booking_adults'   => 2 ], 3 ],
			[ [	'booking_adults'   => 3 ], 3 ],
			[ [	'booking_adults'   => 4 ], 3 ],
			[ [	'booking_adults'   => 5 ], 2 ],
			[ [	'booking_adults'   => 6 ], 2 ],
			[ [	'booking_adults'   => 7 ], 1 ],
			[ [	'booking_adults'   => 8 ], 0 ],
			[ [	'booking_adults'   => 9 ], 0 ],

			// Test children.
			[ [	'booking_children' => 1 ], 3 ],
			[ [	'booking_children' => 2 ], 3 ],
			[ [	'booking_children' => 3 ], 2 ],
			[ [	'booking_children' => 4 ], 1 ],
			[ [	'booking_children' => 5 ], 1 ],
			[ [	'booking_children' => 6 ], 0 ],
			[ [	'booking_children' => 7 ], 0 ],
			[ [	'booking_children' => 8 ], 0 ],

			// Test infants.
			[ [	'booking_infants'   => 1 ], 3 ],
			[ [	'booking_infants'   => 2 ], 3 ],
			[ [	'booking_infants'   => 3 ], 3 ],
			[ [	'booking_infants'   => 4 ], 2 ],
			[ [	'booking_infants'   => 5 ], 1 ],
			[ [	'booking_infants'   => 6 ], 0 ],
			[ [	'booking_infants'   => 7 ], 0 ],
			[ [	'booking_infants'   => 8 ], 0 ],

			// Test min nights
			[ [	'booking_nights'   => 1 ], 0 ],
			[ [	'booking_nights'   => 2 ], 1 ],
			[ [	'booking_nights'   => 3 ], 2 ],
			[ [	'booking_nights'   => 4 ], 3 ],
			[ [	'booking_nights'   => 5 ], 3 ],
			[ [	'booking_nights'   => 6 ], 3 ],

			// Test adults + children
			[ [
				'booking_adults'   => 1,
				'booking_children' => 1,
			], 3 ],

			[ [
				'booking_adults'   => 5,
				'booking_children' => 1,
			], 2 ],

			[ [
				'booking_adults'   => 1,
				'booking_children' => 4,
			], 1 ],

			[ [
				'booking_adults'   => 6,
				'booking_children' => 5,
			], 0 ],

			// Test adults + infants.
			[ [
				'booking_adults'   => 1,
				'booking_infants'   => 1,
			], 3 ],

			[ [
				'booking_adults'   => 1,
				'booking_infants'   => 4,
			], 2 ],

			[ [
				'booking_adults'   => 5,
				'booking_infants'   => 1,
			], 2 ],

			[ [
				'booking_adults'   => 5,
				'booking_infants'   => 1,
			], 2 ],

			[ [
				'booking_adults'   => 6,
				'booking_infants'   => 6,
			], 0 ],

			// Test children + infants.
			[ [
				'booking_children' => 1,
				'booking_infants'   => 1,
			], 3 ],

			[ [
				'booking_children' => 1,
				'booking_infants'   => 5,
			], 1 ],

			[ [
				'booking_children' => 3,
				'booking_infants'   => 5,
			], 1 ],

			[ [
				'booking_children' => 5,
				'booking_infants'   => 6,
			], 0 ],

			// Test adults + children + infants.
			[ [
				'booking_adults'   => 1,
				'booking_children' => 1,
				'booking_infants'   => 1,
			], 3 ],

			[ [
				'booking_adults'   => 3,
				'booking_children' => 4,
				'booking_infants'   => 2,
			], 1 ],

			[ [
				'booking_adults'   => 5,
				'booking_children' => 2,
				'booking_infants'   => 3,
			], 2 ],

			[ [
				'booking_adults'   => 5,
				'booking_children' => 5,
				'booking_infants'   => 5,
			], 0 ],

			// Test adults + childrent + infants + min nights.
			[ [
				'booking_adults'   => 5,
				'booking_children' => 2,
				'booking_infants'   => 3,
				'booking_nights'   => 3,
			], 1 ],

			[ [
				'booking_adults'   => 1,
				'booking_children' => 2,
				'booking_infants'   => 4,
				'booking_nights'   => 2,
			], 1 ],

			[ [
				'booking_adults'   => 3,
				'booking_children' => 3,
				'booking_infants'   => 3,
				'booking_nights'   => 3,
			], 2 ],

			[ [
				'booking_adults'   => 4,
				'booking_children' => 4,
				'booking_infants'   => 4,
				'booking_nights'   => 4,
			], 0 ],
		];
	}
}
