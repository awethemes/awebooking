<?php

use AweBooking\Component\Currency\ISO4217;

class Component_ISO4217_Test extends \WP_UnitTestCase {

	/** @var array */
	public $foo = [
		ISO4217::KEY_NAME => 'FO',
		ISO4217::KEY_ALPHA3 => 'FOO',
	];

	/** @var array */
	public $bar = [
		ISO4217::KEY_NAME => 'BA',
		ISO4217::KEY_ALPHA3 => 'BAR',
	];

	/** @var ISO4217 */
	public $iso4217;

	public function setUp() {
		parent::setUp();

		$this->iso4217 = new ISO4217( [ $this->foo, $this->bar ] );
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

		$this->iso4217->alpha3( $alpha3 );
	}

	/**
	 * @return array
	 */
	public function invalidAlpha3Provider() {
		$invalidNumeric = sprintf( '{^Not a valid %s key: .*$}', ISO4217::KEY_ALPHA3 );
		$noMatch = sprintf( '{^No "%s" key found matching: .*$}', ISO4217::KEY_ALPHA3 );

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
		$this->assertEquals( $this->foo, $this->iso4217->alpha3( $this->foo[ ISO4217::KEY_ALPHA3 ] ) );
		$this->assertEquals( $this->bar, $this->iso4217->alpha3( $this->bar[ ISO4217::KEY_ALPHA3 ] ) );
	}

	/**
	 * @testdox Calling getAll returns an array with all elements.
	 */
	public function testGetAll() {
		$this->assertInternalType( 'array', $this->iso4217->all(), 'getAll() should return an array.' );
	}

	/**
	 * @testdox Iterating over $instance should behave as expected.
	 */
	public function testIterator() {
		$i = 0;
		foreach ( $this->iso4217 as $key => $value ) {
			++$i;
		}

		$this->assertEquals( count( $this->iso4217->all() ), $i, 'Compare iterated count to count(getAll()).' );
	}
}
