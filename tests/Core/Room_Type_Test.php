<?php

class Room_Type_Test extends WP_UnitTestCase {
	/**
	 * Set up the test fixture.
	 */
	public function setUp() {
		parent::setUp();

		$this->postid = $this->factory->post->create([
			'post_title' => 'VIP',
			'post_type'  => 'room_type',
		]);

		add_post_meta( $this->postid, 'base_price', '150' );
		add_post_meta( $this->postid, 'number_adults', '2' );
		add_post_meta( $this->postid, 'number_children', '1' );
		add_post_meta( $this->postid, 'max_adults', '1' );
		add_post_meta( $this->postid, 'max_children', '1' );
		add_post_meta( $this->postid, 'minimum_night', '1' );
	}

	public function test_room_type_object() {
		$room_type = new AweBooking\Room_Type( $this->postid );
		$this->assertInstanceOf( 'AweBooking\\Support\\WP_Object', $room_type );

		$this->assertEquals($room_type['title'], 'VIP');

		$this->assertSame($room_type['base_price'], 150.00);
		$this->assertSame($room_type['number_adults'], 2);
		$this->assertSame($room_type['number_children'], 1);
		$this->assertSame($room_type['max_adults'], 1);
		$this->assertSame($room_type['max_children'], 1);
		$this->assertSame($room_type['minimum_night'], 1);
	}

	public function test_no_exists_room_type() {
		$room_type = new AweBooking\Room_Type( 0 );
		$this->assertFalse($room_type->exists());
	}
}
