<?php

use Ruler\Context;
use Ruler\Variable;
use AweBooking\Component\Ruler\Operator\Not_Between;
use AweBooking\Support\Carbonate;

class NotBetweenTest extends \WP_UnitTestCase {

	public function testInterface() {
		$op = new Not_Between( new Variable( 'a', 1 ), new Variable( 'b', 2 ) );
		$this->assertInstanceOf( 'Ruler\Proposition', $op );
	}

	public function testConstructorAndEvaluation() {
		$context = new Context();

		$op = new Not_Between( new Variable( 'a', 5 ), new Variable( 'b', [1, 10] ) );
		$this->assertFalse( $op->evaluate( $context ) );

		$context['a'] = 1;
		$this->assertFalse( $op->evaluate( $context ) );

		$context['a'] = 10;
		$this->assertFalse( $op->evaluate( $context ) );

		$context['a'] = -1;
		$this->assertTrue( $op->evaluate( $context ) );

		$context['a'] = 11;
		$this->assertTrue( $op->evaluate( $context ) );
	}

	public function testComparisonDatetime() {
		$context = new Context();

		$op = new Not_Between(
			new Variable( 'left', Carbonate::create_date( '2017-10-10' ) ),
			new Variable( 'right', [ Carbonate::create_date( '2017-10-01' ), Carbonate::create_date( '2017-10-11' ) ] )
		);
		$this->assertFalse( $op->evaluate( $context ) );

		$context['left'] = Carbonate::create_date( '2017-10-05' );
		$this->assertFalse( $op->evaluate( $context ) );

		$context['left'] = Carbonate::create_date( '2017-10-11' );
		$this->assertFalse( $op->evaluate( $context ) );

		$context['left'] = Carbonate::create_date( '2017-10-01' );
		$this->assertFalse( $op->evaluate( $context ) );

		// ...
		$context['left'] = Carbonate::create_date( '2017-10-12' );
		$this->assertTrue( $op->evaluate( $context ) );

		$context['left'] = Carbonate::create_date( '2016-09-08' );
		$this->assertTrue( $op->evaluate( $context ) );
	}
}
