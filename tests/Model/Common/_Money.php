<?php

use AweBooking\Model\Common\Money;

class Money_Money_Test extends WP_UnitTestCase {
	public function testConstructor() {
		$money = new Money( 10 );
		$this->assertEquals(10, $money->as_numeric());

		$this->assertEquals(0, Money::zero()->as_numeric());
	}

	public function testProxyMethods() {
		$money = Money::of( 10 );

		// Boolean return.
		$this->assertTrue( $money->equals( 10 ) );
		$this->assertFalse( $money->not_equals( 10 ) );

		$this->assertTrue( $money->greater_than( 5 ) );
		$this->assertTrue( $money->greater_than_or_equal( 10 ) );
		$this->assertFalse( $money->greater_than( 10 ) );
		$this->assertFalse( $money->greater_than_or_equal( 11 ) );

		$this->assertTrue( $money->less_than( 15 ) );
		$this->assertTrue( $money->less_than_or_equal( 10 ) );
		$this->assertFalse( $money->less_than( 10 ) );
		$this->assertFalse( $money->less_than_or_equal( 5 ) );

		$this->assertTrue(Money::of(0)->is_zero());
		$this->assertFalse(Money::of(5)->is_zero());

		$this->assertTrue(Money::of(5)->is_positive());
		$this->assertFalse(Money::of(-5)->is_positive());

		$this->assertTrue(Money::of(-5)->is_negative());
		$this->assertFalse(Money::of(5)->is_negative());

		// New instance return.
		$this->assertEquals($money->as_numeric(), $money->add(0)->as_numeric());
		$this->assertEquals(20, $money->add(10)->as_numeric());
		$this->assertNotSame($money, $money->add(0));

		$this->assertEquals(5, $money->sub(5)->as_numeric());
		$this->assertEquals(5, $money->subtract(5)->as_numeric());
		$this->assertNotSame($money, $money->subtract(0));

		$this->assertEquals(20, $money->mul(2)->as_numeric());
		$this->assertEquals(20, $money->multiply(2)->as_numeric());
		$this->assertNotSame($money, $money->multiply(1));

		$this->assertEquals(5, $money->div(2)->as_numeric());
		$this->assertEquals(5, $money->divide(2)->as_numeric());
		$this->assertNotSame($money, $money->divide(1));

		$this->assertEquals( 5, Money::of(-5)->abs()->as_numeric());
		$this->assertNotSame($money, $money->abs());

		$this->assertEquals(7, $money->to_percentage(70)->as_numeric());
		$this->assertNotSame($money, $money->to_percentage(100));

		$this->assertEquals(8.5, $money->discount(15)->as_numeric());
		$this->assertNotSame($money, $money->discount(0));

		// Float return.
		$this->assertEquals(50, $money->percentage_of(20));
		$this->assertEquals(90, $money->discount_percentage_of(100));
	}
}
