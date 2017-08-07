<?php

use AweBooking\Support\Collection;

class Collection_Test extends WP_UnitTestCase {
	public function testArrayable() {
		$collect = new Collection([
			'a' => 1,
			'b' => 2,
		]);

		$this->assertInstanceOf("AweBooking\\Interfaces\\Arrayable", $collect);
		$this->assertSame($collect->to_array(), $collect->toArray());
	}

	public function testJsonable() {
		$collect = new Collection([
			'a' => 1,
			'b' => 2,
		]);

		$this->assertInstanceOf("AweBooking\\Interfaces\\Jsonable", $collect);
		$this->assertSame($collect->to_json(), $collect->toJson());
	}
}
