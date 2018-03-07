<?php

namespace Ruler\Test\Operator;

use Ruler\Context;
use Ruler\Variable;
use AweBooking\Ruler\Operator\Ends_With;
use AweBooking\Ruler\Operator\Not_Ends_With;

class EndsWithTest extends \WP_UnitTestCase {

	public function testInterface() {
		$varA = new Variable( 'a', 'foo bar baz' );
		$varB = new Variable( 'b', 'foo' );

		$op = new Ends_With( $varA, $varB );
		$this->assertInstanceOf( 'Ruler\Proposition', $op );
	}

	/**
	 * @dataProvider endsWithData
	 */
	public function testEndsWith( $a, $b, $result ) {
		$varA    = new Variable( 'a', $a );
		$varB    = new Variable( 'b', $b );
		$context = new Context();

		$op = new Ends_With( $varA, $varB );
		$this->assertEquals( $op->evaluate( $context ), $result );
	}

	/**
	 * @dataProvider endsWithData
	 */
	public function testNotEndsWith( $a, $b, $result ) {
		$varA    = new Variable( 'a', $a );
		$varB    = new Variable( 'b', $b );
		$context = new Context();

		$op = new Not_Ends_With( $varA, $varB );
		$this->assertNotEquals( $op->evaluate( $context ), $result );
	}

	public function endsWithData() {
		return array(
			array( 'supercalifragilistic', 'supercalifragilistic', true ),
			array( 'supercalifragilistic', 'stic', true ),
			array( 'supercalifragilistic', 'STIC', false ),
			array( 'supercalifragilistic', 'super', false ),
			array( 'supercalifragilistic', '', false ),
		);
	}
}
