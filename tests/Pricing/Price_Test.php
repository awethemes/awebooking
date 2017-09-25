<?php

use AweBooking\Pricing\Price;
use AweBooking\Currency\Currency;

class Price_Test extends WP_UnitTestCase {
	function test_price() {
		$price1 = new Price( 100.00 );
		$price1 = $price1->subtract( new Price( 49.00 ) );

		$this->assertEquals( 51.00, $price1->get_amount() );
		$this->assertEquals( 5100, $price1->to_integer() );

		$price2 = new Price( 100.00 );
		$price2 = $price2->add( new Price( 50.00 ) );

		$this->assertEquals( 150.00, $price2->get_amount() );
		$this->assertEquals( 15000, $price2->to_integer() );
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	function test_invalid_price() {
		new Price( 'a100' );
	}

	/**
	 * @expectedException \AweBooking\Pricing\Currency_Mismatch_Exception
	 */
	function test_price_exception() {
		$price1 = new Price( 100.00, new Currency( 'USD' ) );
		$price1->add( new Price( 49.00, new Currency( 'VND' ) ) );
	}

	public function testFactoryMethod() {
		$money = Price::EUR( 25 );
		$this->assertInstanceOf( Price::class, $money );
	}

	public function testFromAmountAndCurrency() {
		// TODO: Set `price_number_decimals` before.
		$money = Price::from_integer( 1099 );
		$this->assertInstanceOf( Price::class, $money );
		$this->assertEquals( $money->get_amount(), 10.99 );
	}

	public function testNumericValues() {
		$money = Price::EUR( '100' );

		$this->assertTrue( $money->equals( Price::EUR( 100 ) ) );
		$this->assertTrue( $money->equals( Price::EUR( 100.00 ) ) );
		$this->assertTrue( $money->equals( Price::EUR( '100.000000' ) ) );
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testNonNumericStringsThrowException() {
		Price::EUR( 'Foo' );
	}

	public function testGetters() {
		$euro = new Currency( 'EUR' );
		$money = new Price( '100', $euro );
		$this->assertEquals( '100', $money->get_amount() );
		$this->assertEquals( $euro, $money->get_currency() );
	}

	public function testAddition() {
		$m1 = new Price( '100', new Currency( 'EUR' ) );
		$m2 = new Price( '100', new Currency( 'EUR' ) );
		$sum = $m1->add( $m2 );
		$expected = new Price( '200', new Currency( 'EUR' ) );

		$this->assertTrue( $sum->equals( $expected ) );

		// Should return a new instance
		$this->assertNotSame( $sum, $m1 );
		$this->assertNotSame( $sum, $m2 );
	}

	public function testAdditionWithDecimals() {
		$m1 = new Price( '100', new Currency( 'EUR' ) );
		$m2 = new Price( '0.01', new Currency( 'EUR' ) );
		$sum = $m1->add( $m2 );
		$expected = new Price( '100.01', new Currency( 'EUR' ) );

		$this->assertTrue( $sum->equals( $expected ) );
	}

	/**
	 * @expectedException \AweBooking\Pricing\Currency_Mismatch_Exception
	 */
	public function testDifferentCurrenciesCannotBeAdded() {
		$m1 = new Price( '100', new Currency( 'EUR' ) );
		$m2 = new Price( '100', new Currency( 'USD' ) );
		$m1->add( $m2 );
	}

	public function testSubtraction() {
		$m1 = new Price( '100', new Currency( 'EUR' ) );
		$m2 = new Price( '200', new Currency( 'EUR' ) );
		$diff = $m1->subtract( $m2 );
		$expected = new Price( '-100', new Currency( 'EUR' ) );

		$this->assertTrue( $diff->equals( $expected ) );

		// Should return a new instance
		$this->assertNotSame( $diff, $m1 );
		$this->assertNotSame( $diff, $m2 );
	}

	public function testSubtractionWithDecimals() {
		$m1 = new Price( '100.01', new Currency( 'EUR' ) );
		$m2 = new Price( '200', new Currency( 'EUR' ) );
		$diff = $m1->subtract( $m2 );
		$expected = new Price( '-99.99', new Currency( 'EUR' ) );

		$this->assertTrue( $diff->equals( $expected ) );
	}

	/**
	 * @expectedException \AweBooking\Pricing\Currency_Mismatch_Exception
	 */
	public function testDifferentCurrenciesCannotBeSubtracted() {
		$m1 = new Price( '100', new Currency( 'EUR' ) );
		$m2 = new Price( '100', new Currency( 'USD' ) );
		$m1->subtract( $m2 );
	}

	public function testMultiplication() {
		$money = new Price( '100', new Currency( 'EUR' ) );
		$expected1 = new Price( '200', new Currency( 'EUR' ) );
		$expected2 = new Price( '101', new Currency( 'EUR' ) );

		$this->assertTrue( $money->multiply( 2 )->equals( $expected1 ) );
		$this->assertTrue( $money->multiply( '1.01' )->equals( $expected2 ) );

		$this->assertNotSame( $money, $money->multiply( 2 ) );
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testInvalidMultiplicationOperand() {
		$money = new Price( '100', new Currency( 'EUR' ) );
		$money->multiply( 'operand' );
	}

	public function testDivision() {
		$money = new Price( '30', new Currency( 'EUR' ) );
		$expected1 = new Price( '15', new Currency( 'EUR' ) );
		$expected2 = new Price( '3.3333333333333', new Currency( 'EUR' ) );
		$expected3 = new Price( '-3', new Currency( 'EUR' ) );

		$this->assertTrue( $money->divide( 2 )->equals( $expected1 ) );
		// $this->assertTrue($money->divide(9)->equals($expected2));
		$this->assertTrue( $money->divide( -10 )->equals( $expected3 ) );

		$this->assertNotSame( $money, $money->divide( 2 ) );
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testDivisorIsNumericZero() {
		$money = new Price( '30', new Currency( 'EUR' ) );
		$money->divide( 0 )->amount();
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testDivisorIsFloatZero() {
		$money = new Price( '30', new Currency( 'EUR' ) );
		$money->divide( 0.0 )->amount();
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testDivisorIsStringZero() {
		$money = new Price( '30', new Currency( 'EUR' ) );
		$money->divide( '0' )->amount();
	}

	public function testConvert() {
		$money = new Price( '100', new Currency( 'EUR' ) );
		$usd = new Currency( 'USD' );

		$expected = new Price( '150', $usd );

		$this->assertTrue( $money->convert( $usd, '1.50' )->equals( $expected ) );
	}

	public function testComparison() {
		$euro1 = new Price( '100', new Currency( 'EUR' ) );
		$euro2 = new Price( '200', new Currency( 'EUR' ) );
		$euro3 = new Price( '100', new Currency( 'EUR' ) );
		$euro4 = new Price( '0', new Currency( 'EUR' ) );
		$euro5 = new Price( '-100', new Currency( 'EUR' ) );
		$euro6 = new Price( '1.1111', new Currency( 'EUR' ) );
		$euro7 = new Price( '1.2222', new Currency( 'EUR' ) );

		$this->assertTrue( $euro2->greater_than( $euro1 ) );
		$this->assertFalse( $euro1->greater_than( $euro2 ) );
		$this->assertTrue( $euro1->less_than( $euro2 ) );
		$this->assertFalse( $euro2->less_than( $euro1 ) );
		$this->assertTrue( $euro1->equals( $euro3 ) );
		$this->assertFalse( $euro1->equals( $euro2 ) );
		$this->assertFalse( $euro6->equals( $euro7 ) );

		$this->assertTrue( $euro1->greater_than_or_equal( $euro3 ) );
		$this->assertTrue( $euro1->less_than_or_equal( $euro3 ) );

		$this->assertFalse( $euro1->greater_than_or_equal( $euro2 ) );
		$this->assertFalse( $euro1->less_than_or_equal( $euro4 ) );

		$this->assertTrue( $euro4->less_than_or_equal( $euro1 ) );
		$this->assertTrue( $euro4->greater_than_or_equal( $euro5 ) );

		$this->assertTrue( $euro6->less_than_or_equal( $euro7 ) );
	}

	public function testPositivity() {
		$euro1 = new Price( '100', new Currency( 'EUR' ) );
		$euro2 = new Price( '0', new Currency( 'EUR' ) );
		$euro3 = new Price( '-100', new Currency( 'EUR' ) );
		$euro4 = new Price( '0.0001', new Currency( 'EUR' ) );

		$this->assertTrue( $euro1->is_positive() );
		$this->assertFalse( $euro1->is_negative() );
		$this->assertFalse( $euro1->is_zero() );

		$this->assertTrue( $euro2->is_zero() );
		$this->assertFalse( $euro2->is_negative() );
		$this->assertFalse( $euro2->is_positive() );

		$this->assertTrue( $euro3->is_negative() );
		$this->assertFalse( $euro3->is_positive() );
		$this->assertFalse( $euro3->is_zero() );

		$this->assertFalse( $euro4->is_zero() );
	}

	/**
	 * @expectedException \AweBooking\Pricing\Currency_Mismatch_Exception
	 */
	public function testDifferentCurrenciesCannotBeCompared() {
		Price::EUR( 1 )->equals( Price::USD( 1 ) );
	}
}
