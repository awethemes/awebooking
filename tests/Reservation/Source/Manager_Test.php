<?php

use AweBooking\Model\Source;
use AweBooking\Reservation\Source\Manager;

class Reservation_Source_Manager_Test extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->source1 = new Source( 'website', 'Website' );
		$this->source2 = new Source( 'email', 'Email' );
		$this->source3 = new Source( 'walk-in', 'Walk-in' );

		$this->manager = new Manager( [ $this->source1, $this->source2 ] );
	}

	public function testGetAndHas() {
		$this->assertCount(2, $this->manager->all());

		$this->assertTrue($this->manager->registered( 'website' ));
		$this->assertTrue($this->manager->registered( 'email' ));
		$this->assertSame($this->source1, $this->manager->get( 'website' ));
		$this->assertSame($this->source2, $this->manager->get( 'email' ));

		$this->assertNull($this->manager->get('walk-in'));
		$this->assertFalse($this->manager->registered('walk-in'));
	}

	public function testRegistered() {
		$this->assertFalse($this->manager->registered('walk-in'));
		$this->assertTrue($this->manager->registered($this->source1));
		$this->assertTrue($this->manager->registered('website'));
	}

	public function testRegisterAndDeregister() {
		$this->assertNull($this->manager->get('walk-in'));

		$this->manager->register($this->source3);
		$this->assertTrue($this->manager->registered( 'walk-in' ));

		$this->manager->deregister('walk-in');
		$this->assertFalse($this->manager->registered( 'walk-in' ));
	}

	public function testDeregister() {
		$this->assertTrue( $this->manager->registered('website') );
		$this->manager->deregister($this->source1);
		$this->assertFalse( $this->manager->registered('website') );
	}

	public function testToCollection() {
		$this->assertInstanceOf('AweBooking\\Support\\Collection', $this->manager->to_collection());
		$this->assertCount(2, $this->manager->to_collection());

		$this->assertEquals($this->source1, $this->manager->to_collection()->get('website'));
		$this->assertEquals($this->source2, $this->manager->to_collection()->get('email'));
	}
}
