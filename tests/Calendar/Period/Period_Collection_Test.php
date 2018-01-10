<?php

use AweBooking\Calendar\Period\Period;
use AweBooking\Calendar\Period\Period_Collection;

class Period_Collection_Test extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$p1 = new Period( '2017-08-24', '2017-08-25' );
		$p2 = new Period( '2017-08-11', '2017-08-16' );
		$p3 = new Period( '2017-08-26', '2017-08-28' );
		$this->periods = [ $p1, $p2, $p3 ];
	}

	public function testSort() {
		$collection = new Period_Collection($this->periods);
		$sorted = $collection->sort();

		$this->assertInstanceOf('AweBooking\Support\Collection', $sorted);
		$this->assertCount(3, $sorted);

		$this->assertEquals($this->periods[1], $sorted[0]);
		$this->assertEquals($this->periods[0], $sorted[1]);
		$this->assertEquals($this->periods[2], $sorted[2]);
	}

	public function testSortWithEmptyArray() {
		$collection = new Period_Collection([]);
		$this->assertEmpty($collection->sort());
	}

	public function testMerge() {
		$collection = new Period_Collection($this->periods);
		$p = $collection->collapse();

		$this->assertInstanceOf(Period::class, $p);
		$this->assertEquals($this->periods[1]->get_start_date()->toDateString(), $p->get_start_date()->toDateString());
		$this->assertEquals($this->periods[2]->get_end_date()->toDateString(), $p->get_end_date()->toDateString());
	}

	public function testMergeWithEmptyArray() {
		$collection = new Period_Collection([]);
		$this->assertNull($collection->collapse());
	}

	public function testMergeWithOnePeriod() {
		$a = new Period( '2017-08-24', '2017-08-25' );
		$collection = new Period_Collection([ $a ]);

		$p = $collection->collapse();
		$this->assertInstanceOf(Period::class, $p);
		$this->assertEquals($a->get_start_date()->toDateString(), $p->get_start_date()->toDateString());
		$this->assertEquals($a->get_end_date()->toDateString(), $p->get_end_date()->toDateString());
	}

	public function testContinuous() {
		$true = [
			new Period( '2017-08-24', '2017-08-25' ),
			new Period( '2017-08-25', '2017-08-26' ),
			new Period( '2017-08-26', '2017-08-27' ),
		];

		$maybe_true = [
			new Period( '2017-08-11', '2017-08-12' ),
			new Period( '2017-08-18', '2017-08-25' ),
			new Period( '2017-08-12', '2017-08-18' ),
		];

		$false = [
			new Period( '2017-08-24', '2017-08-25' ),
			new Period( '2017-08-25', '2017-08-26' ),
			new Period( '2017-08-27', '2017-08-28' ),
		];

		$c1 = new Period_Collection($true);
		$this->assertTrue($c1->is_continuous());
		$this->assertTrue($c1->is_continuous(false));

		$c2 = new Period_Collection($maybe_true);
		$this->assertTrue($c2->is_continuous());
		$this->assertFalse($c2->is_continuous(false));

		$c3 = new Period_Collection($false);
		$this->assertFalse($c3->is_continuous());
		$this->assertFalse($c3->is_continuous(false));
	}

	public function testContinuousWithEmpty() {
		$c1 = new Period_Collection([]);
		$this->assertFalse($c1->is_continuous());
	}

	public function testContinuousWithOnePeriod() {
		$c1 = new Period_Collection([ new Period( '2017-08-24', '2017-08-25' ) ]);
		$this->assertTrue($c1->is_continuous());
	}

	public function testContinuousWithTwoPeriod() {
		$c1 = new Period_Collection([
			new Period( '2017-08-24', '2017-08-25' ),
			new Period( '2017-08-25', '2017-08-26' ),
		]);
		$this->assertTrue($c1->is_continuous());

		$c1 = new Period_Collection([
			new Period( '2017-08-24', '2017-08-25' ),
			new Period( '2017-08-26', '2017-08-27' ),
		]);
		$this->assertFalse($c1->is_continuous());
	}
}
