<?php

use Carbon\Carbon;
use AweBooking\Support\Period;
use AweBooking\Support\Period_Collection;

class Period_Test extends WP_UnitTestCase {
	function test_working_right() {
		$days = new Period( '2017-05-10', '2017-05-20' );
		$days2 = new Period( Carbon::create(2017, 05, 10)->startOfDay(), Carbon::create(2017, 05, 20)->startOfDay() );

		// Same timestamp start and end days.
		$this->assertEquals($days->get_start_date()->getTimestamp(), $days2->get_start_date()->getTimestamp());
		$this->assertEquals($days->get_end_date()->getTimestamp(), $days2->get_end_date()->getTimestamp());
	}

	function test_date_time_interface() {
		$period = new Period(new DateTimeImmutable('2017-05-10'), new DateTimeImmutable('2017-05-20'));

		$this->assertEquals('10/05/2017', $period->get_start_date()->format('d/m/Y'));
		$this->assertEquals('20/05/2017', $period->get_end_date()->format('d/m/Y'));
	}

	/**
	 * @expectedException LogicException
	 */
	function _test_same_day() {
		new Period( '2017-10-05', '2017-10-05', true );
	}

	/**
	 * @expectedException LogicException
	 */
	function test_invalid_day() {
		new Period( '2017-10-05', '2017-10-04', true );
	}
}
