<?php

use AweBooking\Support\Carbonate;

class Carbonate_Test extends WP_UnitTestCase {
	function test_days_in_month() {
		$this->assertEquals(Carbonate::days_in_month(1, 2017), 31);
		$this->assertEquals(Carbonate::days_in_month(2, 2017), 28);

		$this->assertEquals(Carbonate::days_in_month(2, 2018), 28);
		$this->assertEquals(Carbonate::days_in_month(12, 2018), 31);
	}

	public function testValidDateFormat() {
		$this->assertTrue(Carbonate::is_standard_date_format('2017-08-24'));
		$this->assertFalse(Carbonate::is_standard_date_format('21-0-1'));
	}
}
