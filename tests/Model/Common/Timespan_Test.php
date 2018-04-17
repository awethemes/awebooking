<?php

use AweBooking\Support\Period;
use AweBooking\Model\Common\Timespan;

class Model_Timespan_Test extends WP_UnitTestCase {
	public function testConstructor() {
		$timespan = new Timespan( '2017-04-15', '2017-04-16' );
		$this->assertInstanceOf('JsonSerializable', $timespan);
		$this->assertInstanceOf(Period::class, $timespan->to_period());

		$timespan = Timespan::from( '2017-04-15', 5 );
		$this->assertEquals('2017-04-15', $timespan->get_start_date());
		$this->assertEquals('2017-04-20', $timespan->get_end_date());
		$this->assertEquals(5, $timespan->get_nights());
	}

	public function testGetter() {
		$timespan = new Timespan( '2017-04-15', '2017-04-16' );
		$this->assertEquals('2017-04-15', $timespan->start_date);
		$this->assertEquals('2017-04-15', $timespan->get_start_date());

		$this->assertEquals('2017-04-16', $timespan->end_date);
		$this->assertEquals('2017-04-16', $timespan->get_end_date());
	}

	public function testToArray() {
		$timespan = new Timespan( '2017-04-15', '2017-04-15' );

		$this->assertEquals($timespan->to_array(), [
			'nights'     => 0,
			'start_date' => '2017-04-15',
			'end_date'   => '2017-04-15',
		]);
	}

	public function testToPeriod() {
		$timespan = new Timespan( '2017-04-15', '2017-04-16' );

		$this->assertEquals('2017-04-15 00:00:00', $timespan->to_period('daily')->get_start_date());
		$this->assertEquals('2017-04-16 23:59:00', $timespan->to_period('daily')->get_end_date());

		$this->assertEquals('2017-04-15 00:00:00', $timespan->to_period('nightly')->get_start_date());
		$this->assertEquals('2017-04-15 23:59:00', $timespan->to_period('nightly')->get_end_date());
	}
}
