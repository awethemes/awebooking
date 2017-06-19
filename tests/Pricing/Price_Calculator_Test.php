<?php

use AweBooking\Pricing\Price;
use AweBooking\Pricing\Price_Calculator;
use AweBooking\Pricing\Calculator_Handle;

class Class_Calc_Sub1 implements Calculator_Handle {
	public function handle(Price $price) {
		$price = $price->subtract( new Price( 1) );
		return $price;
	}
}

class Class_Calc_Add25 implements Calculator_Handle {
	public function handle(Price $price) {
		$price = $price->add( new Price( 25 ) );
		return $price;
	}
}

class Class_Calc_Add17 implements Calculator_Handle {
	public function handle(Price $price) {
		$price = $price->add( new Price( 17 ) );
		return $price;
	}
}

class Price_Calculator_Test extends WP_UnitTestCase {
	function test_with_zero() {
		$calc = new Price_Calculator( new Price( 0 ) );

		$result = $calc->process();

		$this->assertTrue($result->is_zero());
	}

	function test_via_closure() {
		// Start from $100, and we wan't exaclly $141 after done.
		$calc = new Price_Calculator( new Price( 100.00 ) );

		$calc->pipe(function($price) {
			$price = $price->subtract(new Price( 1 )); // Sub $1, now is $99

			return $price;
		});

		$calc->pipe(function($price) {
			$price = $price->add(new Price(25)); // Add $25, so we have $124 for now.

			return $price;
		});

		$calc->pipe(function($price) {
			$price = $price->add(new Price(17)); // Now is 141, if right.

			return $price;
		});

		$result = $calc->process();

		$this->assertEquals($result, new Price(141));
	}

	function test_via_class() {
		$calc = new Price_Calculator( new Price( 100.00 ) );

		$calc->pipe(new Class_Calc_Sub1);
		$calc->pipe(new Class_Calc_Add25);
		$calc->pipe(new Class_Calc_Add17);

		$this->assertEquals($calc->process(), new Price(141));
	}

	function test_use_through() {
		$calc = new Price_Calculator( new Price( 100.00 ) );

		$calc->through(array(
			new Class_Calc_Sub1,
			new Class_Calc_Add25,
			new Class_Calc_Add17
		));

		$this->assertEquals($calc->process(), new Price(141));
	}

	/**
	 * @expectedException RuntimeException
	 */
	function test_wrong_pipeline() {
		$calc = new Price_Calculator( new Price( 100.00 ));

		$calc->pipe(function($price) {
			return null;
		});

		$calc->process();
	}

	function test_with_negative_number() {
		$calc = new Price_Calculator( new Price( -100.00 ) );

		$calc->pipe(function($price) {
			$price = $price->add( new Price( 10 ) );

			return $price;
		});

		$calc->pipe(function($price) {
			$price = $price->add( new Price( -10 ) );

			return $price;
		});

		$this->assertEquals($calc->process(), new Price( -100 ) );
	}
}
