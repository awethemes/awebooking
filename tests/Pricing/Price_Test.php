<?php

use AweBooking\Pricing\Price;
use AweBooking\Currency\Currency;

class Price_Test extends WP_UnitTestCase {
	function test_price() {
		$price1 = new Price( 100.00 );
		$price1 = $price1->subtract( new Price( 49.00 ) );

		$this->assertEquals(51.00, $price1->get_amount());
		$this->assertEquals(5100, $price1->to_amount());

		$price2 = new Price( 100.00 );
		$price2 = $price2->add( new Price( 50.00 ) );

		$this->assertEquals(150.00, $price2->get_amount());
		$this->assertEquals(15000, $price2->to_amount());

		$price3 = new Price( 100 );
		$this->assertTrue($price3->equals( new Price( 100.00 ) ) );
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	function test_invalid_price() {
		new Price( 'a100' );
	}

	/**
	 * @expectedException \AweBooking\Pricing\CurrencyMismatchException
	 */
	function test_price_exception() {
		$price1 = new Price( 100.00, new Currency('USD' ));
		$price1->add( new Price( 49.00, new Currency('VND') ) );
	}
}
