<?php

use AweBooking\Pricing\Rate;
use AweBooking\Model\Room_Type;

class RateTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$room_type = new Room_Type;
		$room_type['base_price'] = 100;
		$room_type->save();

		$this->room_type = $room_type;
	}

	public function testStandardRate() {
		$standard_rate = new Rate($this->room_type->get_id(), $this->room_type);

		$this->assertTrue($standard_rate->exists());
		$this->assertEquals(0, $standard_rate['order']);
		$this->assertEquals('Standard', $standard_rate['name']);
		$this->assertEquals($standard_rate['base_price'], 100);

		// can't save
		$standard_rate['order'] = 1;
		$standard_rate['name']  = 'Standard';
		$this->assertEquals($standard_rate->save(), false);
	}
}
