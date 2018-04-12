<?php

use AweBooking\Model\Common\Guest_Count;
use AweBooking\Model\Common\Guest_Counts;

class Model_Guest_Counts_Test extends WP_UnitTestCase {
	public function testConstructor() {
		$guests = new Guest_Counts( 10, 2, 1 );

		$this->assertInstanceOf( Guest_Count::class, $guests->get( 'adults' ) );
		$this->assertEquals( 10, $guests->get( 'adults' )->get_count() );

		$this->assertInstanceOf( Guest_Count::class, $guests->get( 'children' ) );
		$this->assertEquals( 2, $guests->get( 'children' )->get_count() );

		$this->assertInstanceOf( Guest_Count::class, $guests->get( 'infants' ) );
		$this->assertEquals( 1, $guests->get( 'infants' )->get_count() );
	}

	public function testPreDefineGuest() {
		$guests = new Guest_Counts( 10 );

		$this->assertInstanceOf( Guest_Count::class, $guests->get_adults() );
		$this->assertNull( $guests->get_children() );
		$this->assertNull( $guests->get_infants() );

		$guests->set_adults( 1 );
		$guests->set_children( 2 );
		$guests->set_infants( 3 );

		$this->assertEquals( 1, $guests->get_adults()->get_count() );
		$this->assertEquals( 2, $guests->get_children()->get_count() );
		$this->assertEquals( 3, $guests->get_infants()->get_count() );
	}

	public function testGetTotal() {
		$guests = new Guest_Counts( 5, 2, 1 );
		$this->assertEquals(8, $guests->total());

		$guests->set_adults( 1 );
		$this->assertEquals(4, $guests->total());
	}

	public function testToString() {
		$guests = new Guest_Counts( 1, 1, 1 );
		$this->assertContains('1 adult', $guests->as_string());
		$this->assertContains('1 child', $guests->as_string());
		$this->assertContains('1 infant', $guests->as_string());

		$guests = new Guest_Counts( 2, 4, 6 );
		$this->assertContains('2 adults', $guests->as_string());
		$this->assertContains('4 children', $guests->as_string());
		$this->assertContains('6 infants', $guests->as_string());
	}
}
