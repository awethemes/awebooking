<?php

use AweBooking\Calendar\Resource\Resource;
use AweBooking\Calendar\Resource\Resource_Collection;

class Calendar_Resource_Collection_Test extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();
	}

	public function testInstance() {
		$collection = new Resource_Collection( [] );
		$this->assertInstanceOf( 'AweBooking\Support\Collection', $collection);
	}

	public function testGetterAndSetter() {
		$resource1 = new Resource( 1 );
		$resource2 = new Resource( 2 );
		$collection = new Resource_Collection( [ $resource1, $resource2 ] );

		$this->assertSame($resource1, $collection->get( 0 ) );
		$this->assertSame($resource2, $collection->get( 1 ) );
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetterFailed() {
		$collection = new Resource_Collection( [] );
		$collection->push( 'Invalid' );
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetterFailed2() {
		$collection = new Resource_Collection( [] );
		$collection->prepend( 'Invalid' );
	}
}
