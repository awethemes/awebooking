<?php

use AweBooking\Support\Traits\Macroable;

class SupportMacroableTest extends WP_UnitTestCase {
	private $macroable;

	public function setUp() {
		$this->macroable = $this->createObjectForTrait();
	}

	private function createObjectForTrait() {
		return $this->getObjectForTrait( Macroable::class );
	}

	public function testRegisterMacro() {
		$macroable = $this->macroable;

		$macroable::macro( __CLASS__, function () {
			return 'Van Anh';
		});

		$this->assertEquals( 'Van Anh', $macroable::{__CLASS__}() );
	}

	public function testRegisterMacroAndCallWithoutStatic() {
		$macroable = $this->macroable;

		$macroable::macro( __CLASS__, function () {
			return 'Van Anh';
		});

		$this->assertEquals( 'Van Anh', $macroable->{__CLASS__}() );
	}

	public function testWhenCallingMacroClosureIsBoundToObject() {
		TestMacroable::macro( 'tryInstance', function () {
			return $this->protectedVariable;
		});

		TestMacroable::macro( 'tryStatic', function () {
			return static::getProtectedStatic();
		});

		$instance = new TestMacroable();

		$result = $instance->tryInstance();
		$this->assertEquals( 'instance', $result );

		$result = TestMacroable::tryStatic();
		$this->assertEquals( 'static', $result );
	}

	public function testClassBasedMacros() {
		TestMacroable::mixin( new TestMixin() );

		$instance = new TestMacroable();

		$this->assertEquals( 'instance-Adam', $instance->methodOne( 'Adam' ) );
	}
}

class TestMacroable {
	use Macroable;

	protected $protectedVariable = 'instance';

	protected static function getProtectedStatic() {
		return 'static';
	}
}

class TestMixin {
	public function methodOne() {
		return function ( $value ) {
			return $this->methodTwo( $value );
		};
	}

	protected function methodTwo() {
		return function ( $value ) {
			return $this->protectedVariable . '-' . $value;
		};
	}
}
