<?php

use AweBooking\Reservation\Source\Store;
use AweBooking\Reservation\Source\WP_Options_Store;

class Reservation_Source_Store_Test extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->store = new WP_Options_Store;
	}

	public function testPut() {
		$this->store->insert([
			'uid' => 'website',
			'type' => 'direct',
		]);

		$this->store->insert([
			'uid' => 'email',
			'type' => 'direct',
		]);
	}
}
