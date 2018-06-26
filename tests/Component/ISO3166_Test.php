<?php

use AweBooking\Component\Country\ISO3166;

class Component_ISO3166_Test extends \WP_UnitTestCase {

	/** @var array */
	public $foo = [
		ISO3166::KEY_ALPHA2 => 'FO',
		ISO3166::KEY_ALPHA3 => 'FOO',
	];

	/** @var array */
	public $bar = [
		ISO3166::KEY_ALPHA2 => 'BA',
		ISO3166::KEY_ALPHA3 => 'BAR',
	];

	/** @var ISO3166 */
	public $iso3166;

	public function setUp() {
		parent::setUp();

		$this->iso3166 = new ISO3166( [ $this->foo, $this->bar ] );
	}

	/**
	 * @testdox Calling getByAlpha2 with bad input throws various exceptions.
	 * @dataProvider invalidAlpha2Provider
	 *
	 * @param string $alpha2
	 * @param string $expectedException
	 * @param string $exceptionPattern
	 */
	public function testGetByAlpha2Invalid( $alpha2, $expectedException, $exceptionPattern ) {
		$this->setExpectedExceptionRegExp( $expectedException, $exceptionPattern );

		$this->iso3166->alpha2( $alpha2 );
	}

	/**
	 * @return array
	 */
	public function invalidAlpha2Provider() {
		$invalidNumeric = sprintf( '{^Not a valid %s key: .*$}', ISO3166::KEY_ALPHA2 );
		$noMatch = sprintf( '{^No "%s" key found matching: .*$}', ISO3166::KEY_ALPHA2 );

		return [
			[ 'A', 'DomainException', $invalidNumeric ],
			[ 'ABC', 'DomainException', $invalidNumeric ],
			[ 11, 'DomainException', $invalidNumeric ],
			[ 'AB', 'OutOfBoundsException', $noMatch ],
		];
	}

	/**
	 * @testdox Calling getByAlpha2 with a known alpha2 returns matching data array.
	 */
	public function testGetByAlpha2() {
		$this->assertEquals( $this->foo, $this->iso3166->alpha2( $this->foo[ ISO3166::KEY_ALPHA2 ] ) );
		$this->assertEquals( $this->bar, $this->iso3166->alpha2( $this->bar[ ISO3166::KEY_ALPHA2 ] ) );
	}

	/**
	 * @testdox Calling getByAlpha3 with bad input throws various exceptions.
	 * @dataProvider invalidAlpha3Provider
	 *
	 * @param string $alpha3
	 * @param string $expectedException
	 * @param string $exceptionPattern
	 */
	public function testGetByAlpha3Invalid( $alpha3, $expectedException, $exceptionPattern ) {
		$this->setExpectedExceptionRegExp( $expectedException, $exceptionPattern );

		$this->iso3166->alpha3( $alpha3 );
	}

	/**
	 * @return array
	 */
	public function invalidAlpha3Provider() {
		$invalidNumeric = sprintf( '{^Not a valid %s key: .*$}', ISO3166::KEY_ALPHA3 );
		$noMatch = sprintf( '{^No "%s" key found matching: .*$}', ISO3166::KEY_ALPHA3 );

		return [
			[ 'AB', 'DomainException', $invalidNumeric ],
			[ 'ABCD', 'DomainException', $invalidNumeric ],
			[ 1234, 'DomainException', $invalidNumeric ],
			[ 'ABC', 'OutOfBoundsException', $noMatch ],
		];
	}

	/**
	 * @testdox Calling getByAlpha3 with a known alpha3 returns matching data array.
	 */
	public function testGetByAlpha3() {
		$this->assertEquals( $this->foo, $this->iso3166->alpha3( $this->foo[ ISO3166::KEY_ALPHA3 ] ) );
		$this->assertEquals( $this->bar, $this->iso3166->alpha3( $this->bar[ ISO3166::KEY_ALPHA3 ] ) );
	}

	/**
	 * @testdox Calling getAll returns an array with all elements.
	 */
	public function testGetAll() {
		$this->assertInternalType( 'array', $this->iso3166->all(), 'getAll() should return an array.' );
	}

	/**
	 * @testdox Iterating over $instance should behave as expected.
	 */
	public function testIterator() {
		$i = 0;
		foreach ( $this->iso3166 as $key => $value ) {
			++$i;
		}

		$this->assertEquals( count( $this->iso3166->all() ), $i, 'Compare iterated count to count(getAll()).' );
	}
}
