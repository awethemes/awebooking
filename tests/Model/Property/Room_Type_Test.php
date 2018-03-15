<?php

use AweBooking\Model\Room_Type;

class Model_Room_Type_Test extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();
	}

	public function testDefaultOccupancyAttributes() {
		$room_type = new Room_Type;

		// Default attributes.
		$this->assertEquals( 0, $room_type->get_maximum_occupancy() );
		$this->assertEquals( 0, $room_type->get_number_adults() );
		$this->assertEquals( 0, $room_type->get_number_children() );
		$this->assertEquals( 0, $room_type->get_number_infants() );
		$this->assertFalse( $room_type->is_calculation_infants() );
	}

	public function testOccupancyMappingAttributes() {
		$room_type = new Room_Type;

		// Basic setup.
		$room_type['maximum_occupancy'] = 5;
		$room_type['number_adults'] = 5;
		$room_type['number_children'] = 2;
		$room_type['number_infants'] = 2;
		$room_type['calculation_infants'] = true;

		$saved = $room_type->save();
		$this->assertTrue( $saved );

		$this->assertEquals( 5, get_post_meta( $room_type->get_id(), '_maximum_occupancy', true ) );
		$this->assertEquals( 5, get_post_meta( $room_type->get_id(), 'number_adults', true ) );
		$this->assertEquals( 2, get_post_meta( $room_type->get_id(), 'number_children', true ) );
		$this->assertEquals( 2, get_post_meta( $room_type->get_id(), 'number_infants', true ) );
	}

	public function testOccupancyAttributes() {
		$room_type = new Room_Type;

		// Basic setup.
		$room_type->set_maximum_occupancy( 4 );
		$room_type->set_number_adults( 4 );
		$room_type->set_number_children( 2 );
		$room_type->set_number_infants( 1 );
		$this->assertEquals( 4, $room_type->get_maximum_occupancy() );
		$this->assertEquals( 4, $room_type->get_number_adults() );
		$this->assertEquals( 2, $room_type->get_number_children() );
		$this->assertEquals( 1, $room_type->get_number_infants() );

		// After save.
		$saved = $room_type->save();
		$this->assertTrue( $saved );
		$this->assertInstanceOf('WP_Post', $room_type->get_instance());
		$this->assertEquals( 4, $room_type['maximum_occupancy'] );
		$this->assertEquals( 4, $room_type['number_adults'] );
		$this->assertEquals( 2, $room_type['number_children'] );
		$this->assertEquals( 1, $room_type['number_infants'] );

		// Modify with some incorrect data.
		$room_type['number_adults'] = 5;
		$room_type['number_children'] = -1;
		$room_type['number_infants'] = 3;
		$this->assertEquals( 4, $room_type['number_adults'] );
		$this->assertEquals( 1, $room_type['number_children'] );
		$this->assertEquals( 3, $room_type['number_infants'] );

		// calculation_infants
		$room_type['calculation_infants'] = 1;
		$this->assertTrue( $room_type->is_calculation_infants() );
	}

	public function testWithEmptyRooms() {
		$room_type = new Room_Type;
		$room_type['title'] = 'Luxury';
		$room_type->save();

		$this->assertTrue( $room_type->exists() );
		$this->assertInstanceOf( 'AweBooking\\Support\\Collection', $room_type->get_rooms() );
		$this->assertEquals( 0, $room_type->get_total_rooms() );
		$this->assertEmpty( $room_type->get_room_ids() );
	}

	public function testWithRooms() {
		list( $room_type, $room1, $room2 ) = $this->createRoomType();

		$this->assertInstanceOf( 'AweBooking\\Support\\Collection', $room_type->get_rooms() );
		$this->assertEquals( 2, $room_type->get_total_rooms() );
		$this->assertCount( 2, $room_type->get_room_ids() );

		// Get room.
		$this->assertInstanceOf( 'AweBooking\\Model\\Room', $room_type->get_room( $room1 ) );
		$this->assertSame( $room1, $room_type->get_room( $room1 )->get_id() );
		$this->assertSame( $room2, $room_type->get_room( $room2 )->get_id() );
		$this->assertNull( $room_type->get_room( 0 ) );

		// Has room.
		$this->assertTrue( $room_type->has_room( $room1 ) );
		$this->assertTrue( $room_type->has_room( $room2 ) );
		$this->assertFalse( $room_type->has_room( 0 ) );
	}

	public function testRemoveRooms() {
		list( $room_type, $room1, $room2 ) = $this->createRoomType();
		$this->assertEquals( 2, $room_type->get_total_rooms() );

		$removed = $room_type->remove_room( $room1 );
		$this->assertTrue( $removed );
		$this->assertEquals( 1, $room_type->get_total_rooms() );

		$this->assertFalse( $room_type->has_room( $room1 ) );
		$this->assertTrue( $room_type->has_room( $room2 ) );
	}

	public function testAddRooms() {
		list( $room_type, $room1, $room2 ) = $this->createRoomType();
		$this->assertEquals( 2, $room_type->get_total_rooms() );

		$room = $room_type->add_room([
			'name'  => 'A102',
			'order' => 2,
		]);

		$this->assertInstanceOf( 'AweBooking\\Model\\Room', $room );
		$this->assertEquals( 'A102', $room->get_name() );
		$this->assertEquals( 2, $room->get_order() );

		$this->assertEquals( 3, $room_type->get_total_rooms() );
		$this->assertTrue( $room_type->has_room( $room ) );
	}

	protected function createRoomType() {
		$room_type = new Room_Type;
		$room_type['title'] = 'Luxury';

		$room_type->save();
		$rooms = $this->setupRoomUnits( $room_type );

		return [ $room_type, $rooms[0], $rooms[1] ];
	}

	protected function setupRoomUnits( $room_type ) {
		global $wpdb;

		$wpdb->insert( $wpdb->prefix . 'awebooking_rooms', [
			'name' => 'A101',
			'room_type' => $room_type->get_id(),
		]);
		$inserted1 = $wpdb->insert_id;

		$wpdb->insert( $wpdb->prefix . 'awebooking_rooms', [
			'name' => 'A102',
			'room_type' => $room_type->get_id(),
		]);
		$inserted2 = $wpdb->insert_id;

		return [ $inserted1, $inserted2 ];
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
	}
}
