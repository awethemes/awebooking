<?php

use Ruler\Context;
use Ruler\Variable;
use AweBooking\Component\Ruler\Operator\Is_Null;
use AweBooking\Support\Carbonate;

class IsNullTest extends \WP_UnitTestCase {

	public function testInterface() {
		$op = new Is_Null( new Variable( 'var' ) );
		$this->assertInstanceOf( 'Ruler\Proposition', $op );
	}

	public function testConstructorAndEvaluation() {
		$context = new Context();

		$op = new Is_Null( new Variable( 'a' ) );
		$this->assertTrue( $op->evaluate( $context ) );

		$context['a'] = null;
		$this->assertTrue( $op->evaluate( $context ) );

		// False
		$context['a'] = 0;
		$this->assertFalse( $op->evaluate( $context ) );

		$context['a'] = Carbonate::today();
		$this->assertFalse( $op->evaluate( $context ) );

		$context['a'] = true;
		$this->assertFalse( $op->evaluate( $context ) );

		$context['a'] = false;
		$this->assertFalse( $op->evaluate( $context ) );

		$context['a'] = '';
		$this->assertFalse( $op->evaluate( $context ) );

		$context['a'] = 'NULL';
		$this->assertFalse( $op->evaluate( $context ) );
	}
}
