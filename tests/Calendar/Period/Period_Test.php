<?php

use Carbon\Carbon;
use AweBooking\Calendar\Period\Period;
use AweBooking\Calendar\Period\Period_Collection;

class Period_Test extends WP_UnitTestCase {
	public function testSplitByDays() {
		$period = new Period( '2018-03-10', '2018-03-20' );
		$days = $period->split_by_days( [ 1, 2 ] ); // With no args.

		$this->assertInstanceOf(Period_Collection::class, $days);

		dump($days);
		dump($days->merge_continuous());
	}

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
	function test_wrong_format() {
		new Period( '10-10-2017', '10-05-2017', true );
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
