<?php

use Ruler\Context;
use Ruler\Variable;
use AweBooking\Component\Ruler\Operator\Less_Than;
use AweBooking\Support\Carbonate;

class LessThanTest extends \WP_UnitTestCase {

	public function testInterface() {
		$op = new Less_Than( new Variable( 'a', 1 ),  new Variable( 'b', 2 ) );
		$this->assertInstanceOf( 'Ruler\Proposition', $op );
	}

	public function testConstructorAndEvaluation() {
		$context = new Context();

		$op = new Less_Than( new Variable( 'a', 1 ), new Variable( 'b', 2 ) );
		$this->assertTrue( $op->evaluate( $context ) );

		$context['a'] = 2;
		$this->assertFalse( $op->evaluate( $context ) );

		$context['a'] = 3;
		$context['b'] = function () {
			return 1;
		};
		$this->assertFalse( $op->evaluate( $context ) );
	}

	public function testComparisonDatetime() {
		$context = new Context();

		$op = new Less_Than(
			new Variable( 'left', Carbonate::create_date( '2017-10-10' ) ),
			new Variable( 'right', Carbonate::create_date( '2017-10-11' ) )
		);

		$this->assertTrue( $op->evaluate( $context ) );

		$context['right'] = Carbonate::create_date( '2017-10-12' );
		$this->assertTrue( $op->evaluate( $context ) );

		$context['right'] = Carbonate::create_date( '2018-11-25' );
		$this->assertTrue( $op->evaluate( $context ) );

		// ...
		$context['right'] = Carbonate::create_date( '2017-10-10' );
		$this->assertFalse( $op->evaluate( $context ) );

		$context['right'] = Carbonate::create_date( '2016-09-08' );
		$this->assertFalse( $op->evaluate( $context ) );
	}
}
