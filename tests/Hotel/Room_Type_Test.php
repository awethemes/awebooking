<?php

use AweBooking\Hotel\Room_Type;

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
		$room_type = new Room_Type( $this->postid );
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
		$room_type = new Room_Type( 0 );
		$this->assertFalse($room_type->exists());
	}

	public function testDelete() {
		$post_id = $this->factory->post->create([ 'post_type' => 'room_type' ]);
		$room_type = new Room_Type( $post_id );

		$this->assertTrue($room_type->delete());
		$this->assertFalse($room_type->exists());
		$this->assertEquals('trash', get_post_status( $post_id ) );

		$room_type = new Room_Type( $post_id );
		$this->assertTrue($room_type->delete(true));
		$this->assertNull(get_post($post_id));

		$room_type->save();
		$this->assertNotSame($room_type->get_id(), $post_id);
	}

	public function testInsert() {
		$room_type = new Room_Type;
		$room_type['title'] = 'Luxury';
		$room_type['description'] = 'Desc';
		$room_type['short_description'] = 'Short Desc';

		$room_type['base_price'] = 125;
		$room_type['number_adults'] = 2;
		$room_type['number_children'] = 1;
		$room_type->save();

		$post = get_post( $room_type->get_id() );
		$this->assertEquals($room_type['title'], $post->post_title);
		$this->assertEquals($room_type['description'], $post->post_content);
		$this->assertEquals($room_type['short_description'], $post->post_excerpt);

		$this->assertEquals($room_type['number_adults'], get_post_meta( $post->ID, 'number_adults', true ));
		$this->assertEquals($room_type['number_children'], get_post_meta( $post->ID, 'number_children', true ));
	}
}
