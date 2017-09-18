<?php

use AweBooking\Support\Addon;

class Test_Addon extends Addon {
	public function register() {
		$this->awebooking['aaa'] = 10001;
	}

	public function init() {
		$this->awebooking['bbbb'] = 1000;
	}
}

class Test_Require_Addon extends Addon {
	public function requires() {
		return '3.0.0-beta6';
	}
}

class AweBooking_Test extends WP_UnitTestCase {
	/**
	 * Set up the test fixture.
	 */
	public function setUp() {
		parent::setUp();

		$this->awebooking = awebooking();
	}

	public function test_addon() {
		$this->assertNull($this->awebooking->get_addon('awethemes.test_addon'));
		$this->awebooking->register_addon( new Test_Addon('test_addon', __FILE__) );
		$this->assertFalse($this->awebooking->get_addon('awethemes.test_addon')->has_errors());
		$this->assertInstanceOf('Test_Addon', $this->awebooking->get_addon('awethemes.test_addon'));

		$this->assertEquals($this->awebooking['aaa'], 10001);
		$this->assertEquals($this->awebooking['bbbb'], 1000);
	}

	public function testFailedAddon() {
		$this->awebooking->register_addon( new Test_Require_Addon('addon', __FILE__) );
		$this->assertTrue($this->awebooking->get_addon('awethemes.addon')->has_errors());
		// var_dump($this->awebooking->get_addon('addon')->get_errors());
	}

	public function test_instance() {
		$this->assertClassHasStaticAttribute( 'instance', 'AweBooking' );
		$this->assertClassHasStaticAttribute( 'instance', 'AweBooking\\AweBooking' );
	}

	public function test_class_instances() {
		$this->assertInstanceOf( 'AweBooking\\Currency\\Currency', $this->awebooking['currency'] );
		$this->assertInstanceOf( 'AweBooking\\Currency\\Currency_Manager', $this->awebooking['currency_manager'] );

		$this->assertInstanceOf( 'AweBooking\\Booking\\Store', $this->awebooking['store.booking'] );
		$this->assertInstanceOf( 'AweBooking\\Booking\\Store', $this->awebooking['store.pricing'] );
		$this->assertInstanceOf( 'AweBooking\\Booking\\Store', $this->awebooking['store.availability'] );

		$this->assertInstanceOf( 'AweBooking\\Support\\Flash_Message', $this->awebooking['flash_message'] );
	}
}
