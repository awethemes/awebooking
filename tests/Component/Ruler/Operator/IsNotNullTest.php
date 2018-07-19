<?php

use Ruler\Context;
use Ruler\Variable;
use AweBooking\Component\Ruler\Operator\Is_Not_Null;
use AweBooking\Support\Carbonate;

class IsNotNullTest extends \WP_UnitTestCase {

	public function testInterface() {
		$op = new Is_Not_Null( new Variable( 'var' ) );
		$this->assertInstanceOf( 'Ruler\Proposition', $op );
	}

	public function testConstructorAndEvaluation() {
		$context = new Context();

		$op = new Is_Not_Null( new Variable( 'a' ) );
		$this->assertFalse( $op->evaluate( $context ) );

		$context['a'] = null;
		$this->assertFalse( $op->evaluate( $context ) );

		// False
		$context['a'] = 0;
		$this->assertTrue( $op->evaluate( $context ) );

		$context['a'] = Carbonate::today();
		$this->assertTrue( $op->evaluate( $context ) );

		$context['a'] = true;
		$this->assertTrue( $op->evaluate( $context ) );

		$context['a'] = false;
		$this->assertTrue( $op->evaluate( $context ) );

		$context['a'] = '';
		$this->assertTrue( $op->evaluate( $context ) );

		$context['a'] = 'NULL';
		$this->assertTrue( $op->evaluate( $context ) );
	}
}
