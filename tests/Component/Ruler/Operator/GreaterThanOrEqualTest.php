<?php

use Ruler\Context;
use Ruler\Variable;
use AweBooking\Component\Ruler\Operator\Greater_Than_Or_Equal;
use AweBooking\Support\Carbonate;

class GreaterThanOrEqualTest extends \WP_UnitTestCase {

	public function testInterface() {
		$varA = new Variable( 'a', 1 );
		$varB = new Variable( 'b', 2 );

		$op = new Greater_Than_Or_Equal( $varA, $varB );
		$this->assertInstanceOf( 'Ruler\Proposition', $op );
	}

	public function testConstructorAndEvaluation() {
		$varA    = new Variable( 'a', 1 );
		$varB    = new Variable( 'b', 2 );
		$context = new Context();

		$op = new Greater_Than_Or_Equal( $varA, $varB );
		$this->assertFalse( $op->evaluate( $context ) );

		$context['a'] = 2;
		$this->assertTrue( $op->evaluate( $context ) );

		$context['a'] = 3;
		$context['b'] = function () {
			return 3;
		};
		$this->assertTrue( $op->evaluate( $context ) );

		$context['4'] = 3;
		$this->assertTrue( $op->evaluate( $context ) );
	}

	public function testComparisonDatetime() {
		$context = new Context();

		$op = new Greater_Than_Or_Equal(
			new Variable( 'left', Carbonate::create_date( '2017-10-11' ) ),
			new Variable( 'right', Carbonate::create_date( '2017-10-10' ) )
		);

		$this->assertTrue( $op->evaluate( $context ) );

		$context['right'] = Carbonate::create_date( '2017-10-11' );
		$this->assertTrue( $op->evaluate( $context ) );

		$context['right'] = Carbonate::create_date( '2016-08-25' );
		$this->assertTrue( $op->evaluate( $context ) );

		// ...
		$context['right'] = Carbonate::create_date( '2017-10-12' );
		$this->assertFalse( $op->evaluate( $context ) );

		$context['right'] = Carbonate::create_date( '2018-09-08' );
		$this->assertFalse( $op->evaluate( $context ) );
	}
}
