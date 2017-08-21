<?php

class AweBooking_Test extends WP_UnitTestCase {
	/**
	 * Set up the test fixture.
	 */
	public function setUp() {
		parent::setUp();

		$this->awebooking = awebooking();
	}

	public function test_instance() {
		$this->assertClassHasStaticAttribute( 'instance', 'AweBooking' );
		$this->assertClassHasStaticAttribute( 'instance', 'AweBooking\\AweBooking' );
	}

	public function test_class_instances() {
		$this->assertInstanceOf( 'Skeleton\\WP_Option', $this->awebooking['wp_option'] );
		$this->assertInstanceOf( 'AweBooking\\Interfaces\\Config', $this->awebooking['config'] );

		$this->assertInstanceOf( 'AweBooking\\Currency\\Currency', $this->awebooking['currency'] );
		$this->assertInstanceOf( 'AweBooking\\Currency\\Currency_Manager', $this->awebooking['currency_manager'] );

		$this->assertInstanceOf( 'AweBooking\\Booking\\Store', $this->awebooking['store.booking'] );
		$this->assertInstanceOf( 'AweBooking\\Booking\\Store', $this->awebooking['store.pricing'] );
		$this->assertInstanceOf( 'AweBooking\\Booking\\Store', $this->awebooking['store.availability'] );

		$this->assertInstanceOf( 'AweBooking\\Support\\Flash_Message', $this->awebooking['flash_message'] );
	}
}
