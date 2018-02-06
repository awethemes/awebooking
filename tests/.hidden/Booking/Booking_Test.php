<?php

use AweBooking\AweBooking;
use AweBooking\Booking\Booking;
use AweBooking\Booking\Items\Booking_Item;

class Test2_Booking_Item extends Booking_Item {
	public function get_type() {
		return 'line_item';
	}
}

class Booking_Test extends WP_UnitTestCase {
	/**
	 * Set up the test fixture.
	 */
	public function setUp() {
		parent::setUp();
	}

	public function testInsertAndUpdateBasic() {
		$booking = new Booking;
		$booking['customer_note'] = 'ABC';

		$booking->save();
		$post = get_post($booking->get_id());

		$this->assertTrue($booking->exists());
		$this->assertEquals($booking['customer_note'], $post->post_excerpt);
		$this->assertEquals($booking['version'], AweBooking::VERSION);
		$this->assertEquals($booking['status'], Booking::PENDING);
		$this->assertEquals($booking['currency'], get_post_meta($booking->get_id(), '_currency', true));

		$booking['status'] = Booking::COMPLETED;
		$booking['customer_note'] = 'AAA';

		$booking->save();
		$post2 = get_post($booking->get_id());

		$this->assertEquals($booking['status'], $post2->post_status);
		$this->assertEquals($booking['customer_note'], $post2->post_excerpt);
	}

	public function testAddItem() {
		$booking = new Booking;

		$item1 = new Test2_Booking_Item();
		$item1->fill(['name' => 'AAA']);

		$item2 = new Test2_Booking_Item();
		$item2->fill(['name' => 'BBB']);

		$booking->add_item( $item1 );
		$booking->add_item( $item2 );
		$booking->save();

		$this->assertTrue($booking->exists());
		$this->assertTrue($item1->exists());
		$this->assertTrue($booking->has_item($item1->get_id()));
		$this->assertTrue($item2->exists());
		$this->assertTrue($booking->has_item($item2->get_id()));
		$this->assertEquals($item1['name'], 'AAA');
		$this->assertEquals($item2['name'], 'BBB');
	}

	public function testRemoveItem() {
		$item1 = new Test2_Booking_Item();
		$item1->fill(['name' => 'AAA']);

		$booking = new Booking;
		$booking->add_item( $item1 );
		$booking->save();

		$this->assertTrue($booking->has_item($item1->get_id()));
		$booking->remove_item($item1);

		$this->assertFalse($booking->has_item($item1->get_id()));
		$booking->save();
		$this->assertFalse($booking->has_item($item1->get_id()));
	}
}
