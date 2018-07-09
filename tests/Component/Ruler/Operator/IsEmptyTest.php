<?php

use Ruler\Context;
use Ruler\Variable;
use AweBooking\Component\Ruler\Operator\Is_Empty;
use AweBooking\Component\Ruler\Operator\Is_Not_Empty;

class IsEmptyTest extends \WP_UnitTestCase {

	public function testInterface() {
		$op = new Is_Empty( new Variable( 'var', null ) );
		$this->assertInstanceOf( 'Ruler\Proposition', $op );
	}

	/**
	 * @dataProvider containsData
	 */
	public function testEmpty( $a, $result ) {
		$op = new Is_Empty( new Variable( 'var', $a ) );
		$this->assertEquals( $op->evaluate( new Context ), $result );
	}

	/**
	 * @dataProvider containsData
	 */
	public function testNotEmpty( $a, $result ) {
		$op = new Is_Not_Empty( new Variable( 'var', $a ) );
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
