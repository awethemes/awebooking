<?php

use AweBooking\BAT\Request;
use AweBooking\Support\Date_Period;
// use AweBooking\Interfaces\Booking_Request;

class Booking_Request_Test extends WP_UnitTestCase {
	/**
	 * Set up the test fixture.
	 */
	public function setUp() {
		parent::setUp();

		$this->period = new Date_Period( '2017-10-10', '2017-10-20', false );
		$this->request = new Request( $this->period, [
			'adults' => 10,
			'children' => 2,
		]);
	}

	public function testBookingRequest() {
		$this->assertSame($this->request->get_adults(), 10);
		$this->assertSame($this->request->get_children(), 2);
	}
}
