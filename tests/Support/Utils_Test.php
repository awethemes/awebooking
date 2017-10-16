<?php

use AweBooking\Support\Utils as U;

class Utils_Test extends WP_UnitTestCase {

	public function testOptional() {
		$this->assertNull( U::optional( null )->something() );
		$this->assertEquals( 10, U::optional(new _test_class_optional)->something() );
	}

	public function testValue() {
		$this->assertEquals( 'foo', value( 'foo' ) );
		$this->assertEquals(
			'foo', value(
				function () {
					return 'foo';
				}
			)
		);
	}

	public function test_rescue() {
		$this->assertEquals(
			U::rescue(
				function () {
					throw new Exception();
				}, 'rescued!'
			), 'rescued!'
		);

		$this->assertEquals(
			U::rescue(
				function () {
					throw new Exception();
				}, function () {
					return 'rescued!';
				}
			), 'rescued!'
		);

		$this->assertEquals(
			U::rescue(
				function () {
					return 'no need to rescue';
				}, 'rescued!'
			), 'no need to rescue'
		);
	}
}

class _test_class_optional {
	public function something() {
		return 10;
	}
}
