<?php

use AweBooking\Support\Date_Utils;

class Functions_Test extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->page_booking    = $this->factory->post->create();
		$this->page_checkout   = $this->factory->post->create();
		$this->page_check_avai = $this->factory->post->create();

		awebooking('wp_option')->set( 'page_check_availability', $this->page_check_avai );
		awebooking('wp_option')->set( 'page_checkout', $this->page_checkout );
		awebooking('wp_option')->set( 'page_booking', $this->page_booking );
	}

	public function test_awebooking_get_page_id() {
		$this->assertEquals(awebooking_get_page_id('check_availability'), $this->page_check_avai);
		$this->assertEquals(awebooking_get_page_id('booking'), $this->page_booking);
		$this->assertEquals(awebooking_get_page_id('checkout'), $this->page_checkout);
	}
}
