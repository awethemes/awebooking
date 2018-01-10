<?php

use AweBooking\Calendar\Resource\Resource;
use AweBooking\Calendar\Resource\Resource_Interface;

class Calendar_Resource_Test extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();
	}

	public function testInstance() {
		$resource = new Resource( 101 );
		$this->assertInstanceOf( Resource_Interface::class, $resource);
		$this->assertEquals(0, $resource->get_value());
	}

	public function testGetAndSet() {
		$resource = new Resource( 101, 4000 );

		$this->assertEquals( 101, $resource->get_id() );
		$this->assertEquals( 4000, $resource->get_value() );
		$this->assertNull( $resource->get_title() );
		$this->assertNull( $resource->get_description() );

		$resource->set_id( 102 );
		$resource->set_value( 4001 );
		$resource->set_title( 'Title' );
		$resource->set_description( 'Desc' );

		$this->assertEquals( 102, $resource->get_id() );
		$this->assertEquals( 4001, $resource->get_value() );
		$this->assertEquals( 'Title', $resource->get_title() );
		$this->assertEquals( 'Desc', $resource->get_description() );
	}
}
