<?php

use AweBooking\Reservation\Item;

class Frontend_Reservation_Item_Test extends WP_UnitTestCase {
	public function testBasicItem() {
		$item = new Item([
			'id'      => 10,
			'name'    => 'Room Type 1',
			'price'   => 100,
			'options' => [
				'a' => 'b',
				'b' => 'c',
			],
		]);

		$this->assertEquals( 10, $item->get_id());
		$this->assertEquals( 100, $item->get_single_price_exc_tax()->as_numeric());
		$this->assertEquals( 'Room Type 1', $item->get_name());
	}
}
