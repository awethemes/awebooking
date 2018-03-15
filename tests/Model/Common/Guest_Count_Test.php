<?php

use AweBooking\Model\Common\Guest_Count;
use AweBooking\Support\Contracts\Stringable;

class Model_Guest_Count_Test extends WP_UnitTestCase {
	public function testConstructor() {
		$guest = new Guest_Count( 'adults', 2, '>= 10' );

		$this->assertEquals($guest->get_age_code(), 'adults');
		$this->assertEquals($guest->get_age(), '>= 10');
		$this->assertEquals($guest->get_count(), 2);
	}

	public function testSetter() {
		$guest = new Guest_Count( 'adults', 1 );

		$guest->set_age_code( 'adult' );
		$guest->set_age( '>= 5' );
		$guest->set_count( 10 );

		$this->assertEquals($guest->get_age_code(), 'adult');
		$this->assertEquals($guest->get_age(), '>= 5');
		$this->assertEquals($guest->get_count(), 10);
	}

	public function testToString() {
		$guest = new Guest_Count( 'adults', 1 );

		$this->assertInstanceOf(Stringable::class, $guest);
		$this->assertEquals($guest->as_string(), 1);
		$this->assertEquals($guest->__toString(), 1);
	}
}
