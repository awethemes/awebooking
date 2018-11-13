<?php

use AweBooking\Component\Ruler\Context;
use AweBooking\Component\Ruler\Variable;
use AweBooking\Component\Ruler\Variable_Property;

class Rulesest extends \WP_UnitTestCase {
	public function testConstructor() {
		$name = 'evil';
		$var  = new Variable( $name );

		$this->assertEquals( $name, $var->getName() );
		$this->assertNull( $var->getValue() );
	}

	public function testGetSetValue() {
		$values   = explode( ', ', 'Plug it, play it, burn it, rip it, drag and drop it, zip, unzip it' );
		$variable = new Variable( 'technologic' );

		foreach ( $values as $valueString ) {
			$variable->setValue( $valueString );
			$this->assertEquals( $valueString, $variable->getValue() );
		}
	}

	public function testProperties() {
		$var = new Variable;
		$this->assertInstanceOf( 'ArrayAccess', $var );

		$foo = $var['foo'];
		$bar = $var['bar'];
		$this->assertInstanceOf( Variable_Property::class, $foo );
		$this->assertInstanceOf( Variable_Property::class, $bar );

		$this->assertSame( $var['foo'], $foo );
		$this->assertSame( $var['bar'], $bar );
		$this->assertNotSame( $foo, $bar );

		$this->assertTrue( isset( $var['foo'] ) );
		$this->assertTrue( isset( $var['bar'] ) );
		$this->assertFalse( isset( $var['baz'] ) );
		$this->assertFalse( isset( $var['qux'] ) );

		$baz = $var->get_property( 'baz' );
		$this->assertTrue( isset( $var['baz'] ) );

		$qux = $var['qux'];
		$this->assertTrue( isset( $var['qux'] ) );

		unset( $var['foo'], $var['bar'], $var['baz'] );
		$this->assertFalse( isset( $var['foo'] ) );
		$this->assertFalse( isset( $var['bar'] ) );
		$this->assertFalse( isset( $var['baz'] ) );
		$this->assertTrue( isset( $var['qux'] ) );
	}

	public function testDynamicCallOpeators() {
		$context = new Context( [
			'a' => 1,
			'b' => 2,
			'c' => [ 1, 4 ],
			'd' => [
				'foo' => 1,
				'bar' => 2,
				'baz' => [
					'qux' => 3,
				],
			],
			'e' => 1.5,
		] );

		$varA = new Variable( 'a' );
		$varB = new Variable( 'b' );
		$varC = new Variable( 'c' );
		$varD = new Variable( 'd' );
		$varE = new Variable( 'e' );

		// Compare operators.
		$this->assertInstanceOf( 'AweBooking\Component\Ruler\Operator\Equal', $varA->equal( 0 ) );
		$this->assertTrue( $varA->equal( 1 )->evaluate( $context ) );
		$this->assertFalse( $varA->equal( 0 )->evaluate( $context ) );
		$this->assertFalse( $varA->equal( 2 )->evaluate( $context ) );

		$this->assertInstanceOf( 'AweBooking\Component\Ruler\Operator\Not_Equal', $varA->not_equal( 0 ) );
		$this->assertFalse( $varA->not_equal( 1 )->evaluate( $context ) );
		$this->assertTrue( $varA->not_equal( 0 )->evaluate( $context ) );
		$this->assertTrue( $varA->not_equal( 2 )->evaluate( $context ) );

		$this->assertInstanceOf( 'AweBooking\Component\Ruler\Operator\Greater_Than', $varA->greater_than( 0 ) );
		$this->assertTrue( $varA->greater_than( 0 )->evaluate( $context ) );
		$this->assertFalse( $varA->greater_than( 2 )->evaluate( $context ) );

		$this->assertInstanceOf( 'AweBooking\Component\Ruler\Operator\Greater_Than_Or_Equal', $varA->greater_than_or_equal( 0 ) );
		$this->assertTrue( $varA->greater_than_or_equal( 0 )->evaluate( $context ) );
		$this->assertTrue( $varA->greater_than_or_equal( 1 )->evaluate( $context ) );
		$this->assertFalse( $varA->greater_than_or_equal( 2 )->evaluate( $context ) );

		$this->assertInstanceOf( 'AweBooking\Component\Ruler\Operator\Less_Than', $varA->less_than( 0 ) );
		$this->assertTrue( $varA->less_than( 2 )->evaluate( $context ) );
		$this->assertFalse( $varA->less_than( 0 )->evaluate( $context ) );

		$this->assertInstanceOf( 'AweBooking\Component\Ruler\Operator\Less_Than_Or_Equal', $varA->less_than_or_equal( 0 ) );
		$this->assertTrue( $varA->less_than_or_equal( 1 )->evaluate( $context ) );
		$this->assertTrue( $varA->less_than_or_equal( 2 )->evaluate( $context ) );
		$this->assertFalse( $varA->less_than_or_equal( 0 )->evaluate( $context ) );

		$this->assertFalse( $varA->greater_than( $varB )->evaluate( $context ) );
		$this->assertTrue( $varA->less_than( $varB )->evaluate( $context ) );

		// Mathematical operators.
		/*$this->assertInstanceof( Variable::class, $varA->add( 3 ) );
		$this->assertInstanceof( 'Ruler\Operator\Addition', $varA->add( 3 )->getValue() );
		$this->assertInstanceof( 'Ruler\Value', $varA->add( 3 )->prepareValue( $context ) );
		$this->assertEquals( 4, $varA->add( 3 )->prepareValue( $context )->getValue() );
		$this->assertEquals( 0, $varA->add( - 1 )->prepareValue( $context )->getValue() );

		$this->assertInstanceof( Variable::class, $varE->ceil() );
		$this->assertInstanceof( 'Ruler\Operator\Ceil', $varE->ceil()->getValue() );
		$this->assertEquals( 2, $varE->ceil()->prepareValue( $context )->getValue() );

		$this->assertInstanceof( Variable::class, $varB->divide( 3 ) );
		$this->assertInstanceof( 'Ruler\Operator\Division', $varB->divide( 3 )->getValue() );
		$this->assertEquals( 1, $varB->divide( 2 )->prepareValue( $context )->getValue() );
		$this->assertEquals( - 2, $varB->divide( - 1 )->prepareValue( $context )->getValue() );

		$this->assertInstanceof( Variable::class, $varE->floor() );
		$this->assertInstanceof( 'Ruler\Operator\Floor', $varE->floor()->getValue() );
		$this->assertEquals( 1, $varE->floor()->prepareValue( $context )->getValue() );

		$this->assertInstanceof( Variable::class, $varA->modulo( 3 ) );
		$this->assertInstanceof( 'Ruler\Operator\Modulo', $varA->modulo( 3 )->getValue() );
		$this->assertEquals( 1, $varA->modulo( 3 )->prepareValue( $context )->getValue() );
		$this->assertEquals( 0, $varB->modulo( 2 )->prepareValue( $context )->getValue() );

		$this->assertInstanceof( Variable::class, $varA->multiply( 3 ) );
		$this->assertInstanceof( 'Ruler\Operator\Multiplication', $varA->multiply( 3 )->getValue() );
		$this->assertEquals( 6, $varB->multiply( 3 )->prepareValue( $context )->getValue() );
		$this->assertEquals( - 2, $varB->multiply( - 1 )->prepareValue( $context )->getValue() );

		$this->assertInstanceof( Variable::class, $varA->negate() );
		$this->assertInstanceof( 'Ruler\Operator\Negation', $varA->negate()->getValue() );
		$this->assertEquals( - 1, $varA->negate()->prepareValue( $context )->getValue() );
		$this->assertEquals( - 2, $varB->negate()->prepareValue( $context )->getValue() );

		$this->assertInstanceof( Variable::class, $varA->subtract( 3 ) );
		$this->assertInstanceof( 'Ruler\Operator\Subtraction', $varA->subtract( 3 )->getValue() );
		$this->assertEquals( - 2, $varA->subtract( 3 )->prepareValue( $context )->getValue() );
		$this->assertEquals( 2, $varA->subtract( - 1 )->prepareValue( $context )->getValue() );

		$this->assertInstanceof( Variable::class, $varA->exponentiate( 3 ) );
		$this->assertInstanceof( 'Ruler\Operator\Exponentiate', $varA->exponentiate( 3 )->getValue() );
		$this->assertEquals( 1, $varA->exponentiate( 3 )->prepareValue( $context )->getValue() );
		$this->assertEquals( 1, $varA->exponentiate( - 1 )->prepareValue( $context )->getValue() );
		$this->assertEquals( 8, $varB->exponentiate( 3 )->prepareValue( $context )->getValue() );
		$this->assertEquals( 0.5, $varB->exponentiate( - 1 )->prepareValue( $context )->getValue() );*/
	}
}
