<?php

use AweBooking\Support\Decimal;

class DecimalTest extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
	}

	public function testRepresentations() {
		$value = Decimal::create( 10.0, 4 );

		$this->assertEquals( 100000, $value->as_raw_value() );
		$this->assertEquals( 10.0, $value->as_numeric() );
		$this->assertSame( '10.0000', $value->as_string() );
	}

	public function testAsString() {
		$value = Decimal::create( 10.0, 4 );

		$this->assertSame( '10.0000', (string) $value );
		$this->assertSame( '10.0000', $value->as_string() );
		$this->assertSame( '10.00', $value->as_string( 2 ) );
		$this->assertSame( '10', $value->as_string( 0 ) );

		$otherScale = Decimal::create( 15.99, 6 );

		$this->assertSame( '15.990000', (string) $otherScale );
		$this->assertSame( '15.990000', $otherScale->as_string() );
		$this->assertSame( '15.99', $otherScale->as_string( 2 ) );
		$this->assertSame( '15.9', $otherScale->as_string( 1 ) );
		$this->assertSame( '15', $otherScale->as_string( 0 ) );
	}

	public function testAsStringNoScaleRoundsToNextInteger() {
		$noScale = Decimal::create( 15.99, 0 );

		$this->assertSame( '16', (string) $noScale );
		$this->assertSame( '16', $noScale->as_string() );
		$this->assertSame( '16.00000', $noScale->as_string( 5 ) );
		$this->assertSame( '16.00', $noScale->as_string( 2 ) );
		$this->assertSame( '16.0', $noScale->as_string( 1 ) );
		$this->assertSame( '16', $noScale->as_string( 0 ) );
	}

	/**
	 * @expectedException \DomainException
	 */
	public function testInvalidScaleThrowsException() {
		Decimal::create( 10000, -1 );
	}

	/**
	 * @dataProvider createDataProvider
	 */
	public function testCreate( $input, string $expected ) {
		$value = Decimal::create( $input );

		$this->assertSame( $expected, $value->as_string() );
	}

	/**
	 * @dataProvider createZeroScaleDataProvider
	 */
	public function testZeroScale( $input ) {
		$val = Decimal::create( $input, 0 );

		$this->assertEquals( 16, $val->as_raw_value() );
		$this->assertEquals( 16.0, $val->as_numeric() );
		$this->assertEquals( '16.0000', $val->as_string() );
	}

	public function testCreateZero() {
		$zero = Decimal::zero();

		$this->assertEquals( 0, $zero->as_raw_value() );
		$this->assertEquals( 0, $zero->as_numeric() );
		$this->assertEquals( '0.0000', $zero->as_string() );
		$this->assertTrue( $zero->equals( Decimal::create( 0 ) ) );
	}

	/**
	 * @expectedException \InvalidArgumentException
	 * @dataProvider invalidValueCreateProvider
	 */
	public function testErrorOnInvalidCreateArgument( $value ) {
		Decimal::create( $value );
	}

	/**
	 * @expectedException \DomainException
	 */
	public function testInvalidScaleThrowsExceptionOnCreate() {
		Decimal::create( '10.0', -1 );
	}

	public function testCreateRounding() {
		$this->assertEquals( 16, Decimal::create( '15.50', 0 )->as_raw_value() );
		$this->assertEquals( 16, Decimal::create( '15.50', 0, PHP_ROUND_HALF_UP )->as_raw_value() );
		$this->assertEquals( 15, Decimal::create( '15.50', 0, PHP_ROUND_HALF_DOWN )->as_raw_value() );
	}

	public function testFromRawValue() {
		$simpleValue = Decimal::from_raw_value( 100000, 4 );

		$this->assertEquals( 100000, $simpleValue->as_raw_value() );
		$this->assertEquals( 10, $simpleValue->as_numeric() );

		$decimalValue = Decimal::from_raw_value( 159900, 4 );

		$this->assertEquals( 159900, $decimalValue->as_raw_value() );
		$this->assertEquals( 15.99, $decimalValue->as_numeric() );
	}

	public function testFromNumeric() {
		$simpleValue = Decimal::from_numeric( 10, 4 );

		$this->assertEquals( 100000, $simpleValue->as_raw_value() );
		$this->assertEquals( 10, $simpleValue->as_numeric() );

		$decimalValue = Decimal::from_numeric( 15.99, 4 );

		$this->assertEquals( 159900, $decimalValue->as_raw_value() );
		$this->assertEquals( 15.99, $decimalValue->as_numeric() );
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testExceptionOnInvalidFromNumeric() {
		Decimal::from_numeric( 'ABC' );
	}

	public function testFromDecimal() {
		$value        = Decimal::from_raw_value( 100000, 4 );
		$createdValue = Decimal::from_decimal( $value, 4 );

		$this->assertEquals( $value, $createdValue );
	}

	public function testFromDecimalWithDifferentScale() {
		$value        = Decimal::from_raw_value( 100000, 4 );
		$createdValue = Decimal::from_decimal( $value, 8 );

		$this->assertEquals( $value->as_numeric(), $createdValue->as_numeric() );
	}

	public function testWithScale() {
		$val = Decimal::create( '10', 4 );

		$this->assertSame( $val, $val->with_scale( 4 ) );

		$this->assertSame( 100000, $val->as_raw_value() );
		$this->assertSame( 10, $val->as_numeric() );

		$val = $val->with_scale( 6 );

		$this->assertSame( 10000000, $val->as_raw_value() );
		$this->assertSame( 10, $val->as_numeric() );

		$val = $val->with_scale( 2 );

		$this->assertSame( 1000, $val->as_raw_value() );
		$this->assertSame( 10, $val->as_numeric() );

		$val = $val->with_scale( 4 );

		$this->assertSame( 100000, $val->as_raw_value() );
		$this->assertSame( 10, $val->as_numeric() );
	}

	public function testWithScaleLosesPrecision() {
		$val = Decimal::create( '15.99', 4 );

		$this->assertSame( 159900, $val->as_raw_value() );
		$this->assertSame( 15.99, $val->as_numeric() );

		$val = $val->with_scale( 6 );

		$this->assertSame( 15990000, $val->as_raw_value() );
		$this->assertSame( 15.99, $val->as_numeric() );

		$val = $val->with_scale( 2 );

		$this->assertSame( 1599, $val->as_raw_value() );
		$this->assertSame( 15.99, $val->as_numeric() );

		$val = $val->with_scale( 0 );

		$this->assertSame( 16, $val->as_raw_value() );
		$this->assertSame( 16, $val->as_numeric() );

		$val = $val->with_scale( 4 );

		$this->assertSame( 160000, $val->as_raw_value() );
		$this->assertSame( 16, $val->as_numeric() );
	}

	public function testExceptionOnAddWithMismatchingScale() {
		$valA = Decimal::create( '10', 4 );
		$valB = Decimal::create( '20', 8 );

		$scaledB = $valB->with_scale( 4 );
		$this->assertEquals( $valB->as_numeric(), $scaledB->as_numeric() );

		$this->assertEquals( 30, $valA->add( $scaledB )->as_numeric() );

		$this->expectException( \DomainException::class );
		$valA->add( $valB );
	}

	public function testExceptionOnSubWithMismatchingScale() {
		$valA = Decimal::create( '10', 4 );
		$valB = Decimal::create( '20', 8 );

		$scaledB = $valB->with_scale( 4 );
		$this->assertEquals( $valB->as_numeric(), $scaledB->as_numeric() );

		$this->assertEquals( -10, $valA->sub( $scaledB )->as_numeric() );

		$this->expectException( \DomainException::class );
		$valA->sub( $valB );
	}

	public function testCompare() {
		$a = Decimal::create( 5 );
		$b = Decimal::create( 10 );

		$this->assertTrue( $a->equals( $a ) );
		$this->assertTrue( $b->equals( $b ) );
		$this->assertTrue( $a->equals( Decimal::create( 5 ) ) );
		$this->assertFalse( $a->equals( Decimal::create( 5, 8 ) ) );
		$this->assertFalse( $a->equals( $b ) );
		$this->assertFalse( $b->equals( $a ) );

		$this->assertFalse( $a->not_equals( $a ) );
		$this->assertFalse( $b->not_equals( $b ) );
		$this->assertFalse( $a->not_equals( Decimal::create( 5 ) ) );
		$this->assertTrue( $a->not_equals( Decimal::create( 5, 8 ) ) );
		$this->assertTrue( $a->not_equals( $b ) );
		$this->assertTrue( $b->not_equals( $a ) );

		$this->assertEquals( -1, $a->compare( $b ) );
		$this->assertEquals( 1, $b->compare( $a ) );
		$this->assertEquals( 0, $a->compare( $a ) );
		$this->assertEquals( 0, $b->compare( $b ) );

		$this->assertTrue( $a->less_than( $b ) );
		$this->assertFalse( $a->less_than( $a ) );
		$this->assertFalse( $b->less_than( $a ) );
		$this->assertFalse( $b->less_than( $b ) );

		$this->assertTrue( $a->less_than_or_equal( $a ) );
		$this->assertTrue( $a->less_than_or_equal( $b ) );
		$this->assertFalse( $b->less_than_or_equal( $a ) );
		$this->assertTrue( $b->less_than_or_equal( $b ) );

		$this->assertFalse( $a->greater_than( $a ) );
		$this->assertFalse( $a->greater_than( $b ) );
		$this->assertTrue( $b->greater_than( $a ) );
		$this->assertFalse( $b->greater_than( $b ) );

		$this->assertTrue( $a->greater_than_or_equal( $a ) );
		$this->assertFalse( $a->greater_than_or_equal( $b ) );
		$this->assertTrue( $b->greater_than_or_equal( $a ) );
		$this->assertTrue( $b->greater_than_or_equal( $b ) );
	}

	public function testIsPositive() {
		$this->assertTrue( Decimal::create( 10 )->is_positive() );
		$this->assertTrue( Decimal::create( 1 )->is_positive() );
		$this->assertTrue( Decimal::create( 0.1 )->is_positive() );

		$this->assertFalse( Decimal::create( -0.1 )->is_positive() );
		$this->assertFalse( Decimal::create( -1 )->is_positive() );
		$this->assertFalse( Decimal::create( -10 )->is_positive() );

		$this->assertFalse( Decimal::create( 0 )->is_positive() );
		$this->assertFalse( Decimal::create( 0.00001, 4 )->is_positive() );
	}

	public function testIsNegative() {
		$this->assertFalse( Decimal::create( 10 )->is_negative() );
		$this->assertFalse( Decimal::create( 1 )->is_negative() );
		$this->assertFalse( Decimal::create( 0.1 )->is_negative() );

		$this->assertTrue( Decimal::create( -0.1 )->is_negative() );
		$this->assertTrue( Decimal::create( -1 )->is_negative() );
		$this->assertTrue( Decimal::create( -10 )->is_negative() );

		$this->assertFalse( Decimal::create( 0 )->is_negative() );
		$this->assertFalse( Decimal::create( 0.00001, 4 )->is_negative() );
	}

	public function testIsZero() {
		$this->assertTrue( Decimal::create( 0 )->is_zero() );
		$this->assertTrue( Decimal::create( 0.0 )->is_zero() );
		$this->assertTrue( Decimal::create( '0' )->is_zero() );
		$this->assertTrue( Decimal::create( '0.00' )->is_zero() );
		$this->assertTrue( Decimal::from_raw_value( 0 )->is_zero() );
		$this->assertTrue( Decimal::create( 0.00001, 4 )->is_zero() );

		$this->assertFalse( Decimal::create( 10 )->is_zero() );
		$this->assertFalse( Decimal::create( 0.1 )->is_zero() );
		$this->assertFalse( Decimal::create( -0.1 )->is_zero() );
		$this->assertFalse( Decimal::create( -10 )->is_zero() );
	}

	/**
	 * @dataProvider immutableOperationProvider
	 *
	 * @param $input
	 * @param $expected
	 * @param $operation
	 * @param array     ...$arguments
	 */
	public function testImmutableOperations( int $input, int $expected, $operation, ...$arguments ) {
		$value = Decimal::create( $input );

		/** @var Decimal $result */
		$result = call_user_func_array( [ $value, $operation ], $arguments );

		$this->assertNotSame(
			$value,
			$result,
			sprintf(
				'Decimal::create(%d)->%s(%s) returns a new instance',
				$input,
				$operation,
				implode( ', ', $arguments )
			)
		);

		$this->assertSame(
			$expected,
			$result->as_numeric(),
			sprintf(
				'Decimal::create(%d)->%s(%s)->as_numeric() returns %d',
				$input,
				$operation,
				implode( ', ', $arguments ),
				$expected
			)
		);
	}

	public function testAbs() {
		$a = Decimal::create( 5 );
		$b = Decimal::create( -5 );

		$this->assertSame( $a, $a->abs() );
		$this->assertFalse( $a->equals( $b ) );
		$this->assertTrue( $a->equals( $b->abs() ) );
		$this->assertEquals( 5, $b->abs()->as_numeric() );
	}

	/**
	 * @dataProvider addDataProvider
	 */
	public function testAdd( $a, $b, $expected ) {
		$valA = Decimal::create( $a );

		$this->assertEquals( $expected, $valA->add( $b )->as_numeric() );
	}

	/**
	 * @dataProvider subDataProvider
	 */
	public function testSub( $a, $b, $expected ) {
		$valA = Decimal::create( $a );

		$this->assertEquals( $expected, $valA->sub( $b )->as_numeric() );
	}

	/**
	 * @dataProvider mulDataProvider
	 */
	public function testMul( $a, $b, $expected ) {
		$valA = Decimal::create( $a );

		$this->assertEquals( $expected, $valA->mul( $b )->as_numeric() );
	}

	/**
	 * @dataProvider divDataProvider
	 */
	public function testDiv( $a, $b, $expected ) {
		$val = Decimal::create( $a );

		$this->assertEquals( $expected, $val->div( $b )->as_numeric() );
	}

	/**
	 * @dataProvider zeroDataProvider
	 * @expectedException \LogicException
	 */
	public function testExceptionOnDivisionByZero( $val ) {
		$valA = Decimal::from_raw_value( 159900, 4 );
		$valA->div( Decimal::create( $val ) );
	}

	public function testDivisionByZeroEpsilon() {
		// test division by zero error is thrown when difference from 0 is smaller than epsilon (depending on scale)
		$val = Decimal::create( '10', 4 );
		$val->div( 0.1 );
		$val->div( 0.01 );
		$val->div( 0.001 );
		$val->div( 0.0001 );

		$this->expectException( \LogicException::class );

		$val->div( 0.00001 );
	}

	public function testAdditiveInverse() {
		$this->assertSame( '-15.5000', Decimal::create( '15.50' )->to_additive_inverse()->as_string() );
		$this->assertSame( '15.5000', Decimal::create( '-15.50' )->to_additive_inverse()->as_string() );
		$this->assertSame( 0, Decimal::create( 0 )->to_additive_inverse()->as_numeric() );
	}

	public function testToPercentage() {
		$this->assertEquals( 80, Decimal::create( 100 )->to_percentage( 80 )->as_numeric() );
		$this->assertEquals( 25, Decimal::create( 100 )->to_percentage( 25 )->as_numeric() );
		$this->assertEquals( 25, Decimal::create( 50 )->to_percentage( 50 )->as_numeric() );
		$this->assertEquals( 35, Decimal::create( 100 )->to_percentage( 35 )->as_numeric() );
		$this->assertEquals( 200, Decimal::create( 100 )->to_percentage( 200 )->as_numeric() );
	}

	public function testDiscount() {
		$this->assertEquals( 85, Decimal::create( 100 )->discount( 15 )->as_numeric() );
		$this->assertEquals( 50, Decimal::create( 100 )->discount( 50 )->as_numeric() );
		$this->assertEquals( 70, Decimal::create( 100 )->discount( 30 )->as_numeric() );
	}

	public function testPercentageOf() {
		$origPrice       = Decimal::create( '129.99' );
		$discountedPrice = Decimal::create( '88.00' );

		$this->assertEquals( 68, round( $discountedPrice->percentage_of( $origPrice ), 0 ) );
		$this->assertEquals( 148, round( $origPrice->percentage_of( $discountedPrice ), 0 ) );

		$a = Decimal::create( 100 );
		$b = Decimal::create( 50 );

		$this->assertEquals( 100, $a->percentage_of( $a ) );
		$this->assertEquals( 100, $b->percentage_of( $b ) );
		$this->assertEquals( 200, $a->percentage_of( $b ) );
		$this->assertEquals( 50, $b->percentage_of( $a ) );
	}

	public function testDiscountPercentageOf() {
		$origPrice       = Decimal::create( '129.99' );
		$discountedPrice = Decimal::create( '88.00' );

		$this->assertEquals( 32, round( $discountedPrice->discount_percentage_of( $origPrice ), 0 ) );

		$a = Decimal::create( 100 );
		$b = Decimal::create( 50 );
		$c = Decimal::create( 30 );

		$this->assertEquals( 0, $a->discount_percentage_of( $a ) );
		$this->assertEquals( 0, $b->discount_percentage_of( $b ) );
		$this->assertEquals( -100, $a->discount_percentage_of( $b ) );
		$this->assertEquals( 50, $b->discount_percentage_of( $a ) );
		$this->assertEquals( 70, $c->discount_percentage_of( $a ) );
		$this->assertEquals( 40, $c->discount_percentage_of( $b ) );
	}

	// TODO test overflow/underflow is checked on every possible operation
	public function testIntegerOverflow() {
		$val = Decimal::from_raw_value( 1 );

		// there's a threshold of 1 from the boundary, so the greatest usable int is PHP_INT_MAX - 1
		$other = Decimal::from_raw_value( PHP_INT_MAX - 2 );

		$maxInt = $val->add( $other );

		$this->expectException( \OverflowException::class );

		$maxInt->add( Decimal::from_raw_value( 1 ) );
	}

	public function testIntegerUnderflow() {
		$val = Decimal::from_raw_value( -1 );

		// there's a threshold of 1 from the boundary, so the smallest usable int is -1 * PHP_INT_MAX + 1
		$other = Decimal::from_raw_value( ~PHP_INT_MAX + 2 );

		$minInt = $val->add( $other );

		$this->expectException( \UnderflowException::class );

		$minInt->sub( Decimal::from_raw_value( 1 ) );
	}

	public function createDataProvider() {
		return [
			[ 15.99, '15.9900' ],
			[ '15.99', '15.9900' ],
			[ Decimal::from_raw_value( 159900, 4 ), '15.9900' ],
			[ '1.23', '1.2300' ],
			[ '-1.23', '-1.2300' ],
			[ '-0.5', '-0.5000' ],
			[ '1.9999', '1.9999' ],
			[ '1.999999', '2.0000' ],
			[ '1.999900', '1.9999' ],
			[ '100', '100.0000' ],
			[ '-100', '-100.0000' ],
			[ 100, '100.0000' ],
			[ -100, '-100.0000' ],
			[ 100.00, '100.0000' ],
			[ -100.00, '-100.0000' ],
		];
	}

	public function invalidValueCreateProvider() {
		return [
			[ new \DateTime() ],
			[ true ],
			[ false ],
		];
	}

	public function createZeroScaleDataProvider() {
		return [
			[ 15.99, '16.0000' ],
			[ '15.99', '16.0000' ],
			[ Decimal::from_raw_value( 159900, 4 ), '16.0000' ],
		];
	}

	public function immutableOperationProvider() {
		// operations which are expected to return a new instance
		// [input, expected, operation, ...arguments]
		return [
			[ 100, 100, 'with_scale', 2 ],
			[ -10, 10, 'abs' ],
			[ 100, 110, 'add', 10 ],
			[ 100, 90, 'sub', 10 ],
			[ 100, 300, 'mul', 3 ],
			[ 100, 50, 'div', 2 ],
			[ 100, -100, 'to_additive_inverse' ],
			[ 100, 50, 'to_percentage', 50 ],
			[ 100, 85, 'discount', 15 ],
		];
	}

	public function addDataProvider() {
		return $this->buildPriceOperationInputPairs( 30 );
	}

	public function subDataProvider() {
		return $this->buildPriceOperationInputPairs( 1 );
	}

	public function mulDataProvider() {
		return $this->buildScalarOperationInputPairs( 30 );
	}

	public function divDataProvider() {
		return $this->buildScalarOperationInputPairs( 7.50 );
	}

	public function zeroDataProvider() {
		return [
			[ 0 ],
			[ 0.0 ],
			[ '0' ],
			[ Decimal::create( 0, 4 ) ],
		];
	}

	/**
	 * Pairs for price operations (add, sub)
	 *
	 * @param $expected
	 *
	 * @return array
	 */
	private function buildPriceOperationInputPairs( $expected ) {
		$input = [
			[
				15.50,
				14.50,
			],
			[
				'15.50',
				'14.50',
			],
			[
				Decimal::from_raw_value( 155000 ),
				Decimal::from_raw_value( 145000 ),
			],
		];

		return $this->mixPairs( $input, $expected );
	}

	/**
	 * Pairs for scalar operations (mul, div)
	 *
	 * @param $expected
	 *
	 * @return array
	 */
	private function buildScalarOperationInputPairs( $expected ) {
		$input = [
			[
				15,
				2,
			],
			[
				15.00,
				2.00,
			],
			[
				'15.00',
				'2',
			],
			[
				Decimal::from_raw_value( 150000 ),
				Decimal::from_raw_value( 20000 ),
			],
		];

		return $this->mixPairs( $input, $expected );
	}

	/**
	 * Mixes pairs (creates one pair per possible combination)
	 *
	 * @param array    $input
	 * @param $expected
	 *
	 * @return array
	 */
	private function mixPairs( array $input, $expected ) {
		$data  = [];
		$count = count( $input );

		for ( $i = 0; $i < $count; $i++ ) {
			for ( $j = 0; $j < $count; $j++ ) {
				$data[] = [
					$input[ $i ][0],
					$input[ $j ][1],
					$expected,
				];
			}
		}

		return $data;
	}
}
