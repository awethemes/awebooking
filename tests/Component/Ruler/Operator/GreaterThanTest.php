<?php

use Ruler\Context;
use Ruler\Variable;
use AweBooking\Component\Ruler\Operator\Greater_Than;
use AweBooking\Support\Carbonate;

class GreaterThanTest extends \WP_UnitTestCase {

	public function testInterface() {
		$varA = new Variable( 'a', 1 );
		$varB = new Variable( 'b', 2 );

		$op = new Greater_Than( $varA, $varB );
		$this->assertInstanceOf( 'Ruler\Proposition', $op );
	}

	public function testConstructorAndEvaluation() {
		$varA    = new Variable( 'a', 1 );
		$varB    = new Variable( 'b', 2 );
		$context = new Context();

		$op = new Greater_Than( $varA, $varB );
		$this->assertFalse( $op->evaluate( $context ) );

		$context['a'] = 2;
		$this->assertFalse( $op->evaluate( $context ) );

		$context['a'] = 3;
		$context['b'] = function () {
			return 0;
		};
		$this->assertTrue( $op->evaluate( $context ) );
	}

	public function testComparisonDatetime() {
		$context = new Context();

		$op = new Greater_Than(
			new Variable( 'left', Carbonate::create_date( '2017-10-11' ) ),
			new Variable( 'right', Carbonate::create_date( '2017-10-10' ) )
		);

		$this->assertTrue( $op->evaluate( $context ) );

		$context['right'] = Carbonate::create_date( '2017-10-09' );
		$this->assertTrue( $op->evaluate( $context ) );

		$context['right'] = Carbonate::create_date( '2016-08-25' );
		$this->assertTrue( $op->evaluate( $context ) );

		// ...
		$context['right'] = Carbonate::create_date( '2017-10-11' );
		$this->assertFalse( $op->evaluate( $context ) );

		$context['right'] = Carbonate::create_date( '2018-09-08' );
		$this->assertFalse( $op->evaluate( $context ) );
	}
}
