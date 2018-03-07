<?php

use Ruler\Context;
use Ruler\Variable;
use AweBooking\Ruler\Operator\Is_Empty;
use AweBooking\Ruler\Operator\Is_Not_Empty;

class IsEmptyTest extends \WP_UnitTestCase {

	public function testInterface() {
		$op = new Is_Empty;
		$this->assertInstanceOf( 'Ruler\Proposition', $op );
	}

	/**
	 * @dataProvider containsData
	 */
	public function testEmpty( $a, $result ) {
		$var = new Variable( 'a', $a );

		$op = new Is_Empty( $var );
		$this->assertEquals( $op->evaluate( new Context ), $result );
	}

	/**
	 * @dataProvider containsData
	 */
	public function testNotEmpty( $a, $result ) {
		$var = new Variable( 'a', $a );

		$op = new Is_Not_Empty( $var );
		$this->assertNotEquals( $op->evaluate( new Context ), $result );
	}

	public function containsData() {
		return [
			[ '', true ],
			[ false, true ],
			[ null, true ],
			[ [], true ],
			[ 0, true ],

			[ '0', false ],
			[ true, false ],
		];
	}
}
