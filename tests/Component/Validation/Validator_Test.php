<?php

use AweBooking\Component\Validation\Parser;
use AweBooking\Component\Validation\Validator;

class Component_Validation_Test extends WP_UnitTestCase {
	public function testLoadLanguage() {
		new Validator( [], [] );
	}

	public function testParse() {
		$parser = new Parser();

		$parserd = $parser->parse( 'required|int|in:1,2,3' );
		$this->assertEquals( 'required', $parserd[0][0]);
		$this->assertEquals( 'integer', $parserd[1][0]);
		$this->assertEquals( 'in', $parserd[2][0]);
		$this->assertEquals( [[1,2,3]], $parserd[2][1]);
	}

	public function testParserExplode() {
		$rules1 = [
			'name' => 'required|length:3',
			'email' => 'email|equals:name|in:other,name|creditcard:visa,mastercard'
		];

		$rules2 = [
			'name' => [
				'required',
				'length' => 3,
			],
			'email' => [
				'email',
				'equals'     => 'name',
				'in'         => [ 'other', 'name' ],
				'creditcard' => [ 'visa', 'mastercard' ],
			]
		];

		$this->assertParsed( $rules1 );
		$this->assertParsed( $rules2 );
	}

	protected function assertParsed($rules) {
		$parser = new Parser();
		$parsed1 = $parser->explode( $rules );

		// name
		$this->assertCount( 2, $parsed1['name'] );

		$this->assertEquals( 'required', $parsed1['name'][0][0] );
		$this->assertEquals( [], $parsed1['name'][0][1] );

		$this->assertEquals( 'length', $parsed1['name'][1][0] );
		$this->assertEquals( [3], $parsed1['name'][1][1] );

		// email
		$this->assertCount( 4, $parsed1['email'] );

		$this->assertEquals( 'email', $parsed1['email'][0][0] );
		$this->assertEquals( [], $parsed1['email'][0][1] );

		$this->assertEquals( 'equals', $parsed1['email'][1][0] );
		$this->assertEquals( [ 'name' ], $parsed1['email'][1][1] );

		$this->assertEquals( 'in', $parsed1['email'][2][0] );
		$this->assertEquals( [[ 'other', 'name' ]], $parsed1['email'][2][1] );

		$this->assertEquals( 'creditcard', $parsed1['email'][3][0] );
		$this->assertEquals( [[ 'visa', 'mastercard' ]], $parsed1['email'][3][1] );
	}

	public function testEqualsValid() {
		$v = new Validator( [ 'foo' => 'bar', 'bar' => 'bar' ], [ 'foo' => 'equals:bar' ] );
		$this->assertTrue( $v->validate() );
	}

	public function testEqualsInvalid() {
		$v = new Validator( [ 'foo' => 'foo', 'bar' => 'bar' ], [ 'foo' => 'equals:bar' ] );
		$this->assertFalse( $v->validate() );
	}

	public function testLengthValid() {
		$v = new Validator( [ 'str' => 'happy' ], [ 'str' => [ 'length' => 5 ] ] );
		$this->assertTrue( $v->passes() );
	}

	public function testInValid() {
		$v = new Validator( [ 'color' => 'green' ], [ 'color' => 'in:red,green,blue' ] );
		$this->assertTrue( $v->passes() );
	}

	public function testInValidAssociativeArray() {
		$v = new Validator( [ 'color' => 'green' ] );

		$v->add_rule( 'color', [
			'in' => [
				'red'   => 'Red',
				'green' => 'Green',
				'blue'  => 'Blue',
			],
		] );

		$this->assertTrue( $v->validate() );
	}

	public function testLengthBetweenValid() {
		$v = new Validator( [ 'str' => 'happy' ] );
		$v->add_rule( 'str', 'lengthBetween:2,8' );
		$this->assertTrue( $v->passes() );
	}

	/*public function testCreditCardValid() {
		$visa       = 4539511619543489;
		$mastercard = 5162057048081965;
		$amex       = 371442067262027;
		$dinersclub = 30363194756249;
		$discover   = 6011712400392605;

		foreach ( compact( 'visa', 'mastercard', 'amex', 'dinersclub', 'discover' ) as $type => $number ) {
			$v = new Validator( [ 'test' => $number ] );
			$v->add_rule( 'test', 'required|creditcard' );
			$this->assertTrue( $v->passes() );

			$v = new Validator( [ 'test' => $number ] );
			$v->add_rule( 'test', 'required|creditcard:' . $type );
			$this->assertTrue( $v->passes() );

			$v = new Validator( [ 'test' => $number ] );
			$v->add_rule( 'test', 'required|creditcard:visa,mastercard' );
			$this->assertTrue( $v->passes() );

			unset( $v);
		}

		$v = new Validator( [ 'test' => $discover ], [ 'test' => 'required|creditCard:mastercard' ] );
		$this->assertTrue( $v->fails() );

		$v = new Validator( [ 'test' => $amex ], [ 'test' => 'required|creditCard:visa' ] );
		$this->assertTrue( $v->fails() );
	}*/
}
