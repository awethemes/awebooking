<?php

use AweBooking\Pricing\Price;
use AweBooking\Cart\Item as Cart_Item;

class Cart_Item_Test extends WP_UnitTestCase {
	public function testBasic() {
		$opts = ['a' => 1, 'b' => 2];
		$item = new Cart_Item(100, new Price(99.99), $opts);
		$item->set_quantity(4);

		$this->assertEquals(100, $item->get_id());
		$this->assertEquals(4, $item->get_quantity());
		// $this->assertEquals(md5( 100 . serialize( $opts ) ), $item->get_row_id());

		$this->assertEquals(99.99, $item->get_price()->get_amount());
		$this->assertEquals(399.96, $item->get_subtotal()->get_amount());
		$this->assertEquals(399.96, $item->get_total()->get_amount());

		$this->assertEquals(0, $item->get_tax()->get_amount());
		$this->assertEquals(0, $item->get_tax_total()->get_amount());
		$this->assertEquals(99.99, $item->get_price_with_tax()->get_amount());
	}

	public function testCantSetQuantity() {
		$item = new Cart_Item(100, new Price(99.99));

		$item->set_quantity(-4);
		$this->assertEquals(1, $item->get_quantity());
	}

	public function testWithTax() {
		$item = new Cart_Item(100, new Price(99));
		$item->set_quantity(3);
		$item->set_tax_rate(10);

		$this->assertEquals(9.9, $item->get_tax()->get_amount());
		$this->assertEquals(29.7, $item->get_tax_total()->get_amount());
		$this->assertEquals(108.9, $item->get_price_with_tax()->get_amount());

		$this->assertEquals(99, $item->get_price()->get_amount());
		$this->assertEquals(297, $item->get_subtotal()->get_amount());
		$this->assertEquals(326.7, $item->get_total()->get_amount());
	}

	public function testCantSetTaxRate() {
		$item = new Cart_Item(100, new Price(99));

		$item->set_tax_rate(10);
		$this->assertEquals(10, $item->get_tax_rate());

		$item->set_tax_rate(-1);
		$this->assertEquals(0, $item->get_tax_rate());
	}

	public function testWithAssociate() {
		$item = new Cart_Item(100, new Price(99));
		$item->associate(AweBooking\Hotel\Service::class);

		$this->assertInstanceOf(AweBooking\Hotel\Service::class, $item->model());
		$this->assertEquals($item->model()->get_id(), 100);
	}

	public function testAssociateWrongClass() {
		$item = new Cart_Item(100, new Price(99));
		$item->associate('class-not-found');

		$this->assertInstanceOf(AweBooking\Support\Optional::class, $item->model());
		$this->assertEquals(null, $item->model()->asdasdasdasd);
		$this->assertEquals(null, $item->model()->get_id());
	}

/*	public function test_it_can_be_cast_to_an_array() {
		$cartItem = new Cart_Item(
			1, 'Some item', new Price(10.00), [
				'size' => 'XL',
				'color' => 'red',
			]
		);

		$cartItem->set_quantity( 2 );

		$this->assertEquals(
			[
				'id' => 1,
				'name' => 'Some item',
				'price' => 10.00,
				'row_id' => '07d5da5550494c62daf9993cf954303f',
				'qty' => 2,
				'options' => [
					'size' => 'XL',
					'color' => 'red',
				],
				'tax' => 0,
				'subtotal' => 20.00,
			], $cartItem->toArray()
		);
	}*/
}
