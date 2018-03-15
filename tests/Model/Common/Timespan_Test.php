<?php

use AweBooking\Model\Common\Timespan;
use AweBooking\Support\Contracts\Stringable;

class Model_Timespan_Test extends WP_UnitTestCase {
	public function testConstructor() {
		$timespan = new Timespan( '2017-10-10', new DateTime( '2017-10-11' ) );
		$this->assertInstanceOf(Stringable::class, $timespan);

		$timespan = Timespan::from( '2017-10-10', 1 );
	}

	public function test_nights() {
		$timespan = new Timespan( '2017-10-10', new DateTime( '2017-10-20' ) );
		$this->assertEquals($timespan->nights(), 10);
	}
}
