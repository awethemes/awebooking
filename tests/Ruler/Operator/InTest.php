<?php

use Ruler\Context;
use Ruler\Variable;
use AweBooking\Ruler\Operator\In as In_Operator;
use AweBooking\Ruler\Operator\Not_In;

class InTest extends \WP_UnitTestCase {

	public function testInterface() {
		$varA = new Variable( 'a', 1 );
		$varB = new Variable( 'b', array( 2 ) );

		$op = new In_Operator( $varA, $varB );
		$this->assertInstanceOf( 'Ruler\Proposition', $op );
	}

	/**
	 * @dataProvider containsData
	 */
	public function testContains( $a, $b, $result ) {
		$varA    = new Variable( 'a', $a );
		$varB    = new Variable( 'b', $b );
		$context = new Context();

		$op = new In_Operator( $varA, $varB );
		$this->assertEquals( $op->evaluate( $context ), $result );
	}

	/**
	 * @dataProvider containsData
	 */
	public function testDoesNotContain( $a, $b, $result ) {
		$varA    = new Variable( 'a', $a );
		$varB    = new Variable( 'b', $b );
		$context = new Context();

		$op = new Not_In( $varA, $varB );
		$this->assertNotEquals( $op->evaluate( $context ), $result );
	}

	public function containsData() {
		return array(
			array( array( 1 ), array( 1 ), false ),
			array( 1, array( 1 ), true ),
			array( 2, array( 1, 2, 3 ), true ),
			array( 3, array( 1, 2, 3 ), true ),
		);
	}
}
