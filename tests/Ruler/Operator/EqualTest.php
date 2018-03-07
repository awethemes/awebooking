<?php

namespace Ruler\Test\Operator;

use Ruler\Context;
use Ruler\Variable;
use AweBooking\Ruler\Operator\Equal;
use AweBooking\Support\Carbonate;

class EqualTest extends \WP_UnitTestCase {

	public function testInterface() {
		$varA = new Variable( 'a', 1 );
		$varB = new Variable( 'b', 2 );

		$op = new Equal( $varA, $varB );
		$this->assertInstanceOf( 'Ruler\Proposition', $op );
	}

	public function testConstructorAndEvaluation() {
		$varA    = new Variable( 'a', 1 );
		$varB    = new Variable( 'b', 2 );
		$context = new Context();

		$op = new Equal( $varA, $varB );
		$this->assertFalse( $op->evaluate( $context ) );

		$context['a'] = 2;
		$this->assertTrue( $op->evaluate( $context ) );

		$context['a'] = 3;
		$context['b'] = function () {
			return 3;
		};
		$this->assertTrue( $op->evaluate( $context ) );

		// String
		$context['a'] = 'same';
		$context['b'] = 'same';
		$this->assertTrue( $op->evaluate( $context ) );

		$context['a'] = 'same';
		$context['b'] = 'sAme';
		$this->assertFalse( $op->evaluate( $context ) );

		// Number
		$context['a'] = 0;
		$context['b'] = '0';
		$this->assertTrue( $op->evaluate( $context ) );

		// Datetime
		$context['a'] = '2016-08-25';
		$context['b'] = Carbonate::create_date( '2016-08-25' );
		$this->assertTrue( $op->evaluate( $context ) );

		$context['a'] = '2016-08-26';
		$context['b'] = Carbonate::create_date( '2016-08-25' );
		$this->assertFalse( $op->evaluate( $context ) );

		// Boolean
		$context['a'] = false;
		$context['b'] = 0;
		$this->assertTrue( $op->evaluate( $context ) );

		$context['a'] = true;
		$context['b'] = 1;
		$this->assertTrue( $op->evaluate( $context ) );

		$context['a'] = false;
		$context['b'] = 1;
		$this->assertFalse( $op->evaluate( $context ) );
	}
}
