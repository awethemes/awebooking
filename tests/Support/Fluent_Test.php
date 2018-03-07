<?php

use AweBooking\Support\Fluent;

class Support_Fluent_Test extends WP_UnitTestCase {

	public function testAttributesAreSetByConstructor() {
		$array = [
			'name' => 'Taylor',
			'age' => 25,
		];

		$fluent = new Fluent( $array );

		$refl = new ReflectionObject( $fluent );
		$attributes = $refl->getProperty( 'attributes' );
		$attributes->setAccessible( true );

		$this->assertEquals( $array, $attributes->getValue( $fluent ) );
		$this->assertEquals( $array, $fluent->get_attributes() );
	}

	public function testAttributesAreSetByConstructorGivenstdClass() {
		$array = [
			'name' => 'Taylor',
			'age' => 25,
		];

		$fluent = new Fluent( (object) $array );

		$refl = new ReflectionObject( $fluent );
		$attributes = $refl->getProperty( 'attributes' );
		$attributes->setAccessible( true );

		$this->assertEquals( $array, $attributes->getValue( $fluent ) );
		$this->assertEquals( $array, $fluent->get_attributes() );
	}

	public function testAttributesAreSetByConstructorGivenArrayIterator() {
		$array = [
			'name' => 'Taylor',
			'age' => 25,
		];

		$fluent = new Fluent( new FluentArrayIteratorStub( $array ) );

		$refl = new ReflectionObject( $fluent );
		$attributes = $refl->getProperty( 'attributes' );
		$attributes->setAccessible( true );

		$this->assertEquals( $array, $attributes->getValue( $fluent ) );
		$this->assertEquals( $array, $fluent->get_attributes() );
	}

	public function testGetMethodReturnsAttribute() {
		$fluent = new Fluent( [ 'name' => 'Taylor' ] );

		$this->assertEquals( 'Taylor', $fluent->get( 'name' ) );
		$this->assertEquals( 'Default', $fluent->get( 'foo', 'Default' ) );
		$this->assertEquals( 'Taylor', $fluent->name );
		$this->assertNull( $fluent->foo );
	}

	public function testArrayAccessToAttributes() {
		$fluent = new Fluent( [ 'attributes' => '1' ] );

		$this->assertTrue( isset( $fluent['attributes'] ) );
		$this->assertEquals( $fluent['attributes'], 1 );

		$fluent->attributes();

		$this->assertTrue( $fluent['attributes'] );
	}

	public function testMagicMethodsCanBeUsedToSetAttributes() {
		$fluent = new Fluent();

		$fluent->name = 'Taylor';
		$fluent->developer();
		$fluent->age( 25 );

		$this->assertEquals( 'Taylor', $fluent->name );
		$this->assertTrue( $fluent->developer );
		$this->assertEquals( 25, $fluent->age );
		$this->assertInstanceOf( Fluent::class, $fluent->programmer() );
	}

	public function testIssetMagicMethod() {
		$array = [
			'name' => 'Taylor',
			'age' => 25,
		];

		$fluent = new Fluent( $array );

		$this->assertTrue( isset( $fluent->name ) );

		unset( $fluent->name );

		$this->assertFalse( isset( $fluent->name ) );
	}

	public function testToArrayReturnsAttribute() {
		$array = [
			'name' => 'Taylor',
			'age' => 25,
		];

		$fluent = new Fluent( $array );

		$this->assertEquals( $array, $fluent->toArray() );
	}

	public function testToJsonEncodesTheToArrayResult() {
		$array = [
			'name' => 'Taylor',
			'age' => 25,
		];

		$fluent = new Fluent( $array );
		$this->assertJsonStringEqualsJsonString( json_encode( $array ), $fluent->toJson() );
	}
}

class FluentArrayIteratorStub implements IteratorAggregate {
	protected $items = [];

	public function __construct( array $items = [] ) {
		$this->items = (array) $items;
	}

	public function getIterator() {
		return new \ArrayIterator( $this->items );
	}
}
