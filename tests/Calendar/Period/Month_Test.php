<?php

use AweBooking\Calendar\Period\Month;

class Calendar_Month_Test extends \WP_UnitTestCase {
	/**
	 * @dataProvider providerConstructValid
	 */
	public function testConstructValid( $year, $month ) {
		$year = new Month( $year, $month );
		$this->assertEquals( '2017-11-01', $year->format( 'Y-m-d' ) );
	}

	/**
	 * @dataProvider providerConstructInvalid
	 * @expectedException Exception
	 */
	public function testConstructInvalid( $year, $month ) {
		new Month( $year, $month );
	}

	public function testIteration() {
		$start = new \DateTime('2017-10-30');
		$month  = new Month( 2017, 11 );

		$i = 0;
		foreach ( $month as $weekKey => $week ) {
			$this->assertTrue( is_numeric( $weekKey ) && $weekKey > 0 && $weekKey <= 52 );
			$this->assertInstanceOf( 'AweBooking\\Calendar\\Period\\Week', $week );

			$this->assertSame( $start->format( 'Y-m-d' ), $week->get_start_date()->format( 'Y-m-d' ) );
			$start->add( new \DateInterval( 'P1W' ) );
			$i++;
		}

		$this->assertEquals( $i, 5 );
	}

	public function testNumberOfDay() {
		$month = new Month( 2017, 11 );
		$this->assertSame( 30, $month->get_number_days() );
	}

	public function testToString() {
		$month = new Month( 2017, 11 );
		$this->assertSame( '11', (string) $month );
	}

	public function testFormat() {
		$month = new Month( '2017', 11 );
		$this->assertEquals( '2017-11-01', $month->format( 'Y-m-d' ) );
	}

	public static function providerConstructValid() {
		return array(
			array( 2017, 11 ),
			array( 2017, '11' ),
			array( '2017', 11 ),
			array( new \DateTime( '2017-11-30' ), null ),
			array( new \DateTime( '2017-11-01 00:00' ), null ),
			array( new \DateTimeImmutable( '2017-11-01' ), null ),
		);
	}

	public static function providerConstructInvalid() {
		return array(
			array( 'Invalid year', 10 ),
			array( 2017, -1 ),
			array( 2017, 0 ),
			array( 2017, 13 ),
			array( 2017, null ),
		);
	}
}
