<?php

use Carbon\Carbon;
use AweBooking\Support\Period;
use AweBooking\Support\Period_Collection;

class Period_Test extends WP_UnitTestCase {
	/**
	 * A single example test.
	 */
	function test_working_right() {
		$days = new Period( '2017-05-10', '2017-05-20' );
		$days2 = new Period( Carbon::create(2017, 05, 10)->startOfDay(), Carbon::create(2017, 05, 20)->startOfDay() );

		// Same timestamp start and end days.
		$this->assertEquals($days->get_start_date()->getTimestamp(), $days2->get_start_date()->getTimestamp());
		$this->assertEquals($days->get_end_date()->getTimestamp(), $days2->get_end_date()->getTimestamp());

		// Same timestamp start and end days.
		$this->assertEquals($days->nights(), 10);
		$this->assertEquals($days->nights(), $days2->nights());
	}

	function test_date_time_interface() {
		$period = new Period(new DateTimeImmutable('2017-05-10'), new DateTimeImmutable('2017-05-20'));

		$this->assertEquals('10/05/2017', $period->get_start_date()->format('d/m/Y'));
		$this->assertEquals('20/05/2017', $period->get_end_date()->format('d/m/Y'));
	}

	public function test_segments1() {
		$period = new Period( '2017-10-20', '2017-11-01' );
		$segments = iterator_to_array($period->segments( 1 )); // With start of week is Monday.

		$this->assertCount(3, $segments);
		$this->assertEquals(new Period('2017-10-20', '2017-10-23'), $segments[0]);
		$this->assertEquals(new Period('2017-10-23', '2017-10-30'), $segments[1]);
		$this->assertEquals(new Period('2017-10-30', '2017-11-01'), $segments[2]);

		$this->assertTrue((new Period_Collection($segments))->adjacents());
	}

	public function test_segments2() {
		$period = new Period( '2017-10-21', '2017-10-31' );
		$segments = iterator_to_array($period->segments( 0 )); // With start of week is Monday.

		$this->assertCount(3, $segments);
		$this->assertEquals(new Period('2017-10-21', '2017-10-22'), $segments[0]);
		$this->assertEquals(new Period('2017-10-22', '2017-10-29'), $segments[1]);
		$this->assertEquals(new Period('2017-10-29', '2017-10-31'), $segments[2]);
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
