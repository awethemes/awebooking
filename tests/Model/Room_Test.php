<?php

use AweBooking\Constants;
use AweBooking\Model\Room;
use AweBooking\Model\Room_Type;

class Model_Room_Test extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();
	}

	public function testGetAttributes() {
		$r1 = $this->setupRoomUnits();
		$room = new Room( $r1 );

		$this->assertTrue( $room->exists() );
		$this->assertEquals( $r1, $room->get_id() );
		$this->assertEquals( 'A101', $room->get_name() );
		$this->assertEquals( 0, $room->get_order() );
		$this->assertEquals( 1, $room['room_type'] );
		$this->assertInstanceOf('AweBooking\\Model\\Room_Type', $room->get_room_type() );
	}

	public function testSetRoomType() {
		$room = new Room;

		$room['room_type'] = 100;
		$this->assertEquals( 100, $room['room_type'] );

		$room['room_type'] = new Room_Type( 0 );
		$this->assertEquals( 0, $room['room_type'] );
	}

	public function testWithInstance() {
		$r1 = $this->setupRoomUnits();
		$dbroom = $this->getItemInDB( $r1 );

		$room = (new Room)->with_instance( $dbroom );
		$this->assertTrue( $room->exists() );

		foreach ( $room->only( 'id', 'name', 'room_type', 'order' ) as $key => $value ) {
			$this->assertEquals( $dbroom[ $key ], $value );
		}
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testWithInstanceException() {
		$room = (new Room)->with_instance([
			'id'   => 10,
			'name' => '101',
		]);
	}

	public function testInsertRoom() {
		$room = new Room;

		$room->fill([
			'name'      => 'B101',
			'room_type' => 100,
			'order'     => 20,
		]);

		$room->save();
		$this->assertTrue( $room->exists() );

		$dbroom = $this->getItemInDB( $room->get_id() );
		foreach ( $room->only( 'id', 'name', 'room_type', 'order' ) as $key => $value ) {
			$this->assertEquals( $dbroom[ $key ], $value );
		}
	}

	public function testUpdateRoom() {
		$r1 = $this->setupRoomUnits();

		$room = new Room( $r1 );
		$this->assertTrue( $room->exists() );

		$room['name'] = '101';
		$room['order'] = 20;
		$room['room_type'] = 100;
		$room->save();

		// Assert both data correct.
		$dbroom = $this->getItemInDB( $room->get_id() );
		foreach ( $room->only( 'id', 'name', 'room_type', 'order' ) as $key => $value ) {
			$this->assertEquals( $dbroom[ $key ], $value );
		}
	}

	public function testDeleteRoom() {
		$r1 = $this->setupRoomUnits();

		$room = new Room( $r1 );
		$this->assertTrue( $room->exists() );

		$room->delete();

		$this->assertFalse( $room->exists() );
		$this->assertNull( $this->getItemInDB( $r1 ) );
	}

	public function testGetRoomWithCache() {
		$r1 = $this->setupRoomUnits();

		$room = new Room( $r1 );
		$this->assertTrue( $room->exists() );

		$cache = wp_cache_get( $r1, Constants::CACHE_RAW_ROOM_UNIT );
		$this->assertNotFalse( $cache );

		foreach ( $room->only( 'id', 'name', 'room_type', 'order' ) as $key => $value ) {
			$this->assertEquals( $cache[ $key ], $value );
		}
	}

	public function testCacheWillBeClearAfterUpdate() {
		$r1 = $this->setupRoomUnits();

		$room = new Room( $r1 );
		$this->assertTrue( $room->exists() );
		$this->assertNotFalse( wp_cache_get( $r1, Constants::CACHE_RAW_ROOM_UNIT ) );

		$room['name'] = '101';
		$room->save();
		// The cache will be clear and repleace by new one.

		$cache = wp_cache_get( $r1, Constants::CACHE_RAW_ROOM_UNIT );
		$this->assertNotFalse( $cache );
		$this->assertEquals( '101', $cache['name'] );
	}

	public function testCacheWillbeClearAfterDelete() {
		$r1 = $this->setupRoomUnits();

		$room = new Room( $r1 );
		$this->assertTrue( $room->exists() );
		$this->assertNotFalse( wp_cache_get( $r1, Constants::CACHE_RAW_ROOM_UNIT ) );

		$room->delete();

		$this->assertFalse( $room->exists() );
		$this->assertNull( $this->getItemInDB( $r1 ) );
		$this->assertFalse( wp_cache_get( $r1, Constants::CACHE_RAW_ROOM_UNIT ) );
	}

	protected function setupRoomUnits() {
		global $wpdb;

		$wpdb->insert( $wpdb->prefix . 'awebooking_rooms', [
			'name' => 'A101',
			'room_type' => 1,
		]);

		return $wpdb->insert_id;
	}

	protected function getItemInDB( $id ) {
		global $wpdb;

		return $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}awebooking_rooms` WHERE `id` = '%d' LIMIT 1", $id ),
			ARRAY_A
		);
	}
}
