<?php

use AweBooking\Cart\Cart;
use AweBooking\Cart\Buyable;

class Cart_Test extends WP_UnitTestCase {
	/**
	 * Set up the test fixture.
	 */
	public function setUp() {
		parent::setUp();

		$this->cart = awebooking( Cart::class );
	}

	/**
	 * A single example test.
	 */
	public function testSample() {
		$this->assertEquals( $this->cart, awebooking( 'cart' ) );

		// Replace this with some actual testing code.
		$this->assertTrue( true );
	}
}

class Buyable_Product implements Buyable {
	private $id;
	private $name;
	private $price;

	public function __construct( $id = 1, $name = 'Item name', $price = 10.00 ) {
		$this->id = $id;
		$this->name = $name;
		$this->price = $price;
	}

	public function get_buyable_identifier( $options = null ) {
		return $this->id;
	}

	public function get_buyable_description( $options = null ) {
		return $this->name;
	}

	public function get_buyable_price( $options = null ) {
		return $this->price;
	}

	public function is_purchasable( $options ) {
		return true;
	}
}
