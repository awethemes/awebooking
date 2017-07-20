<?php

use Carbon\Carbon;
use AweBooking\Support\Date_Period;

class Date_Period_Test extends WP_UnitTestCase {
	/**
	 * A single example test.
	 */
	function test_working_right() {
		$days = new Date_Period( '2017-05-10', '2017-05-12', false );

		$days = new Date_Period( '2017-05-10', '2017-05-20', false );
		$days2 = new Date_Period( Carbon::create(2017, 05, 10), Carbon::create(2017, 05, 20), false );

		// Same timestamp start and end days.
		$this->assertEquals($days->get_start_date()->getTimestamp(), $days2->get_start_date()->getTimestamp());
		$this->assertEquals($days->get_end_date()->getTimestamp(), $days2->get_end_date()->getTimestamp());

		// Same timestamp start and end days.
		$this->assertEquals($days->nights(), 10);
		$this->assertEquals($days->nights(), $days2->nights());
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	function test_wrong_format() {
		new Date_Period( '10-10-2017', '10-05-2017' );
	}

	/**
	 * @expectedException LogicException
	 */
	function test_same_day() {
		new Date_Period( '2017-10-05', '2017-10-05' );
	}

	/**
	 * @expectedException LogicException
	 */
	function test_invalid_day() {
		new Date_Period( '2017-10-05', '2017-10-04' );
	}
}
