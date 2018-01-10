<?php

use AweBooking\AweBooking;
use AweBooking\Http\Routing\Binding_Resolver;

class Http_Routing_Binding_Resolver_Test extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->binding = new Binding_Resolver( AweBooking::getInstance() );
	}

	public function testBindingShouldWork() {
		$this->binding->bind( 'name', function( $val ) {
			return 'transform-' . $val;
		});

		$this->binding->bind( 'dash-name', function( $val ) {
			return 'transform-' . $val;
		});

		$this->assertNotNull($this->binding->get_binding_callback( 'name' ));
		$this->assertNotNull($this->binding->get_binding_callback( 'dash-name' ));
		$this->assertNotNull($this->binding->get_binding_callback( 'dash_name' ));
	}

	public function testCallbackBinding() {
		$this->binding->bind( 'name', function( $val ) {
			return 'transform-' . $val;
		});

		$params = $this->binding->resolve( [ 'name' => 'Van Anh', 'times' => 10 ]);
		$this->assertEquals('transform-Van Anh', $params['name']);
		$this->assertEquals(10, $params['times']);
	}

	public function testClassBinding() {
		$this->binding->bind( 'name', 'TestClassForBinding' );

		$params = $this->binding->resolve( [ 'name' => 'Van Anh', 'times' => 10 ]);
		$this->assertEquals('transform-Van Anh', $params['name']);
		$this->assertEquals(10, $params['times']);
	}

	public function testClassWithMethodForBinding() {
		$this->binding->bind( 'name', 'TestClassWithMethodForBinding@getRoom' );

		$params = $this->binding->resolve( [ 'name' => 'Van Anh', 'times' => 10 ]);
		$this->assertEquals('transform-Van Anh', $params['name']);
		$this->assertEquals(10, $params['times']);
	}

	public function testModelBinding() {
		$this->binding->model( 'room', 'TestRoomModelExists' );
		$params = $this->binding->resolve( [ 'room' => 100 ]);
		$this->assertInstanceOf('TestRoomModelExists', $params['room']);
	}

	/**
	 * @expectedException AweBooking\Model\Exceptions\Model_Not_Found_Exception
	 */
	public function testModelBindingNotWork() {
		$this->binding->model( 'room', 'TestRoomModelNotExists' );
		$params = $this->binding->resolve( [ 'room' => 0 ]);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testModelBindingNotWorkWithCustomException() {
		$this->binding->model( 'room', 'TestRoomModelNotExists', function() {
			throw new InvalidArgumentException;
		});

		$params = $this->binding->resolve( [ 'room' => 0 ]);
	}
}

class TestClassForBinding {
	public function bind( $val ) {
		return 'transform-' . $val;
	}
}

class TestClassWithMethodForBinding {
	public function getRoom( $id ) {
		return 'transform-' . $id;
	}
}

class TestRoomModelExists {
	public function exists() {
		return true;
	}
}

class TestRoomModelNotExists {
	public function exists() {
		return false;
	}
}
