<?php

use AweBooking\Calendar\Period\Year;

class Calendar_Year_Test extends \WP_UnitTestCase {
	/**
	 * @dataProvider providerConstructValid
	 */
	public function testConstructValid( $year ) {
		$year = new Year( $year );
		$this->assertEquals( '2017-01-01', $year->format( 'Y-m-d' ) );
	}

	/**
	 * @dataProvider providerConstructInvalid
	 * @expectedException InvalidArgumentException
	 */
	public function testConstructInvalid( $year ) {
		new Year( $year );
	}

	public function testIteration() {
		$start = new \DateTime('2017-01-01');
		$end   = new \DateTime('2017-02-01');
		$year  = new Year( 2017 );

		$i = 0;
		foreach ( $year as $monthKey => $month ) {
			$this->assertTrue( is_numeric( $monthKey ) && $monthKey > 0 && $monthKey <= 12 );
			$this->assertInstanceOf( 'AweBooking\\Calendar\\Period\\Month', $month );

			$this->assertSame( $start->format( 'Y-m-d' ), $month->get_start_date()->format( 'Y-m-d' ) );
			$this->assertSame( $end->format( 'Y-m-d' ), $month->get_end_date()->format( 'Y-m-d' ) );

			$end->add( new \DateInterval( 'P1M' ) );
			$start->add( new \DateInterval( 'P1M' ) );

			$i++;
		}

		$this->assertEquals( $i, 12 );
	}

	public function testToString() {
		$year = new Year( 2017 );
		$this->assertSame( '2017', (string) $year );
	}

	public function testFormat() {
		$year = new Year( '2017' );
		$this->assertEquals( '2017-01-01', $year->format( 'Y-m-d' ) );
	}

	public static function providerConstructValid() {
		return array(
			array( 2017 ),
			array( '2017' ),
			array( new \DateTime( '2017-12-31' ) ),
			array( new \DateTime( '2017-01-01 00:00' ) ),
			array( new \DateTimeImmutable( '2017-01-01' ) ),
		);
	}

	public static function providerConstructInvalid() {
		return array(
			array( 'Invalid' ),
		);
	}
}
