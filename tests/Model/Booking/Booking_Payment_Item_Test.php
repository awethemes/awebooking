<?php

use AweBooking\Support\Decimal;
use AweBooking\Model\Booking_Payment_Item;

class Model_Booking_Payment_Item_Test extends WP_UnitTestCase {
	public function testAttributesBasic() {
		$payment_item = new Booking_Payment_Item;

		$payment_item['name']           = 'Cash';
		$payment_item['method']         = 'cash';
		$payment_item['amount']         = 199.99;
		$payment_item['comment']        = 'no comment';
		$payment_item['booking_id']     = 10;
		$payment_item['transaction_id'] = 'dummy1234';
		$payment_item->save();

		$this->assertTrue($payment_item->exists());
		$this->assertEquals('cash', $payment_item->get_method());
		$this->assertEquals('Cash', $payment_item->get_name());
		$this->assertEquals('no comment', $payment_item->get_comment());
		$this->assertEquals('dummy1234', $payment_item->get_transaction_id());

		$this->assertInstanceOf(Decimal::class, $payment_item->get_amount());
		$this->assertEquals(199.99, $payment_item->get_amount()->as_numeric());
	}
}
