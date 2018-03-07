<?php

use Ruler\Context;
use Ruler\Variable;
use AweBooking\Ruler\Operator\String_Contains;
use AweBooking\Ruler\Operator\String_Does_Not_Contain;

class StringContainsTest extends \WP_UnitTestCase
{
	public function testInterface()
	{
		$varA = new Variable('a', 1);
		$varB = new Variable('b', array(2));

		$op = new String_Contains($varA, $varB);
		$this->assertInstanceOf('Ruler\Proposition', $op);
	}

	/**
	 * @dataProvider containsData
	 */
	public function testContains($a, $b, $result)
	{
		$varA    = new Variable('a', $a);
		$varB    = new Variable('b', $b);
		$context = new Context();

		$op = new String_Contains($varA, $varB);
		$this->assertEquals($op->evaluate($context), $result);
	}

	/**
	 * @dataProvider containsData
	 */
	public function testDoesNotContain($a, $b, $result)
	{
		$varA    = new Variable('a', $a);
		$varB    = new Variable('b', $b);
		$context = new Context();

		$op = new String_Does_Not_Contain($varA, $varB);
		$this->assertNotEquals($op->evaluate($context), $result);
	}

	public function containsData()
	{
		return array(
			array('supercalifragilistic', 'super', true),
			array('supercalifragilistic', 'fragil', true),
			array('supercalifragilistic', 'a', true),
			array('supercalifragilistic', 'stic', true),
			array('timmy', 'bob', false),
			array('tim', 'TIM', false),
		);
	}
}
