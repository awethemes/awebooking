<?php

use AweBooking\Model\Common\Timespan;
use AweBooking\Model\Pricing\Breakdown;

class Model_Pricing_Breakdown_Test extends WP_UnitTestCase {
	/* @var Breakdown */
	protected $breakdown;

	public function setUp() {
		parent::setUp();
		$this->breakdown = new Breakdown( new Timespan( '2018-08-14', '2018-08-24' ), 100 );
	}

	public function testGetBreakdown() {
		$this->assertCount( 10, $this->breakdown->get_breakdown());

		$this->assertEquals( [
			'2018-08-14' => 100,
			'2018-08-15' => 100,
			'2018-08-16' => 100,
			'2018-08-17' => 100,
			'2018-08-18' => 100,
			'2018-08-19' => 100,
			'2018-08-20' => 100,
			'2018-08-21' => 100,
			'2018-08-22' => 100,
			'2018-08-23' => 100,
		], $this->breakdown->get_breakdown() );
	}

	public function testSetBreakdown() {
		$this->breakdown->set( '2018-08-15', 101 );
		$this->breakdown->set( new DateTime('2018-08-23'), 1200 );
		$breakdown = $this->breakdown->get_breakdown();

		$this->assertEquals( 101, $breakdown['2018-08-15']);
		$this->assertEquals( 101, $this->breakdown->get( '2018-08-15' ) );

		$this->assertEquals( 1200, $breakdown['2018-08-23']);
		$this->assertEquals( 1200, $this->breakdown->get( '2018-08-23' ) );
	}

	public function testMergeMethod() {
		$this->breakdown->merge( [
			'2018-08-15' => 11,
			'2018-08-16' => 12,
		]);

		$this->assertEquals( 11, $this->breakdown->get( '2018-08-15' ) );
		$this->assertEquals( 12, $this->breakdown->get( '2018-08-16' ) );
	}

	public function testCalculate() {
		$this->assertEquals( 1000, $this->breakdown->sum() );
		$this->assertEquals( 100, $this->breakdown->avg() );

		$this->breakdown->set( '2018-08-16', 120 );
		$this->assertEquals( 1020, $this->breakdown->sum() );
		$this->assertEquals( 102, $this->breakdown->avg() );
	}
}
