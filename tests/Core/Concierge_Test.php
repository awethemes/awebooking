<?php

use AweBooking\Constants;
use AweBooking\Model\Room;
use AweBooking\Model\Room_Type;

class Concierge_Test extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->luxury = $this->setupLuxuryRoomType();

		$this->rooms  = [];
		foreach ( $this->luxury->get_rooms() as $i => $room ) {
			$this->rooms[$i] = $room;
		}
	}

	public function testSetupCorrect() {
		$this->assertEquals( 'Luxury', $this->luxury->get( 'title' ) );
		$this->assertEquals( 150, $this->luxury->get( 'rack_rate' ) );
		$this->assertEquals( 10, $this->luxury->get( 'maximum_occupancy' ) );
		$this->assertEquals( 2, $this->luxury->get( 'number_adults' ) );
		$this->assertEquals( 2, $this->luxury->get( 'number_children' ) );
		$this->assertCount( 3, $this->luxury->get_rooms());
	}

	public function testInsertAndAssertStateData() {
		$this->insertStateData(1, 2018, 4, [
			'd10' => 1,
			'd11' => 1,
		]);

		$this->assertStateData(1, 2018, 4, [
			'd10' => 1,
			'd11' => 1,
		]);
	}

	public function testCheckRoomState() {
		$room = $this->rooms[1]->get_id();

		$this->insertStateData( $room, 2017, 06, [
			'd6'  => Constants::STATE_UNAVAILABLE,
			'd7'  => Constants::STATE_UNAVAILABLE,
			'd10' => Constants::STATE_BOOKING,
			'd11' => Constants::STATE_BOOKING,
			'd12' => Constants::STATE_BOOKING,
			'd13' => Constants::STATE_BOOKING,
			'd14' => Constants::STATE_BOOKING,
			'd15' => Constants::STATE_BOOKING,
			'd16' => Constants::STATE_UNAVAILABLE,
		]);

		$this->assertTrue( abrs_room_has_states( $room, abrs_timespan( '2017-06-09', '2017-06-10' ), Constants::STATE_AVAILABLE ) );
		$this->assertTrue( abrs_room_has_states( $room, abrs_timespan( '2017-06-06', '2017-06-17' ), [ Constants::STATE_BOOKING, Constants::STATE_UNAVAILABLE, Constants::STATE_AVAILABLE ] ) );
		$this->assertFalse( abrs_room_has_states( $room, abrs_timespan( '2017-06-10', '2017-06-17' ), Constants::STATE_BOOKING ) );

		$this->assertFalse( abrs_room_available( $room, abrs_timespan( '2017-06-06', '2017-06-08' ) ) );
		$this->assertFalse( abrs_room_available( $room, abrs_timespan( '2017-06-07', '2017-06-08' ) ) );
		$this->assertFalse( abrs_room_available( $room, abrs_timespan( '2017-06-10', '2017-06-15' ) ) );
		$this->assertTrue( abrs_room_available( $room, abrs_timespan( '2017-06-08', '2017-06-09' ) ) );
		$this->assertTrue( abrs_room_available( $room, abrs_timespan( '2017-06-09', '2017-06-10' ) ) );

		$this->assertWPError( abrs_room_available( $room, abrs_timespan( '2017-06-08', '2017-06-08' ) ) );
	}


	public function testApplyRoomState() {
		$room = $this->rooms[1]->get_id();

		$this->insertStateData( $room, 2018, 04, [
			'd20' => Constants::STATE_UNAVAILABLE,
			'd21' => Constants::STATE_UNAVAILABLE,
			'd22' => Constants::STATE_BOOKING,
			'd23' => Constants::STATE_BOOKING,
			'd24' => Constants::STATE_UNAVAILABLE,
		]);

		// Error.
		$this->assertWPError( abrs_apply_room_state( $room, abrs_timespan( '2018-04-08', '2018-04-08' ), 1 ) );
		$this->assertWPError( abrs_apply_room_state( -1, abrs_timespan( '2018-04-08', '2018-04-10' ), 1 ) );

		// In free space.
		abrs_db_transaction( 'start' );
		$this->assertTrue( abrs_apply_room_state( $room, abrs_timespan( '2018-04-08', '2018-04-10' ), Constants::STATE_UNAVAILABLE ) );
		$this->assertStateData($room, 2018, 04, [
			'd7' => 0,
			'd8' => Constants::STATE_UNAVAILABLE,
			'd9' => Constants::STATE_UNAVAILABLE,
			'd10' => 0,
		]);
		abrs_db_transaction( 'rollback' );

		// No actions.
		$this->assertNull( abrs_apply_room_state( $room, abrs_timespan( '2018-04-01', '2018-04-05' ), Constants::STATE_AVAILABLE ) );
		$this->assertNull( abrs_apply_room_state( $room, abrs_timespan( '2018-04-20', '2018-04-22' ), Constants::STATE_UNAVAILABLE ) );
		$this->assertNull( abrs_apply_room_state( $room, abrs_timespan( '2018-04-22', '2018-04-24' ), Constants::STATE_BOOKING ) );
		$this->assertNull( abrs_apply_room_state( $room, abrs_timespan( '2018-04-23', '2018-04-24' ), Constants::STATE_UNAVAILABLE ) );

		// OK, but can overwrite booking state.
		abrs_db_transaction( 'start' );
		$this->assertTrue( abrs_apply_room_state( $room, abrs_timespan( '2018-04-20', '2018-04-24' ), Constants::STATE_AVAILABLE ) );
		$this->assertStateData($room, 2018, 04, [
			'd20' => Constants::STATE_AVAILABLE,
			'd21' => Constants::STATE_AVAILABLE,
			'd22' => Constants::STATE_BOOKING,
			'd23' => Constants::STATE_BOOKING,
			'd24' => Constants::STATE_UNAVAILABLE, // exclude this day.
		]);

		$this->assertTrue( abrs_apply_room_state( $room, abrs_timespan( '2018-04-20', '2018-04-24' ), Constants::STATE_UNAVAILABLE ) );
		$this->assertStateData($room, 2018, 04, [
			'd20' => Constants::STATE_UNAVAILABLE,
			'd21' => Constants::STATE_UNAVAILABLE,
			'd22' => Constants::STATE_BOOKING,
			'd23' => Constants::STATE_BOOKING,
			'd24' => Constants::STATE_UNAVAILABLE, // exclude this day.
		]);
		abrs_db_transaction( 'rollback' );

		// OK can overwrite.
		abrs_db_transaction( 'start' );
		$this->assertTrue( abrs_apply_room_state( $room, abrs_timespan( '2018-04-19', '2018-04-24' ), Constants::STATE_BOOKING ) );
		$this->assertStateData($room, 2018, 04, [
			'd19' => Constants::STATE_BOOKING,
			'd20' => Constants::STATE_BOOKING,
			'd21' => Constants::STATE_BOOKING,
			'd22' => Constants::STATE_BOOKING,
			'd23' => Constants::STATE_BOOKING,
			'd24' => Constants::STATE_UNAVAILABLE, // exclude this day.
		]);
		abrs_db_transaction( 'rollback' );

		// By daily
		abrs_db_transaction( 'start' );
		$this->assertTrue( abrs_apply_room_state( $room, abrs_timespan( '2018-04-20', '2018-04-21' ), Constants::STATE_AVAILABLE, [ 'granularity' => Constants::GL_DAILY ] ) );
		$this->assertStateData($room, 2018, 04, [
			'd19' => Constants::STATE_AVAILABLE,
			'd20' => Constants::STATE_AVAILABLE,
			'd21' => Constants::STATE_AVAILABLE,
			'd22' => Constants::STATE_BOOKING,
			'd23' => Constants::STATE_BOOKING,
		]);
		abrs_db_transaction( 'rollback' );

		// By daily
		abrs_db_transaction( 'start' );
		$this->assertTrue( abrs_apply_room_state( $room, abrs_timespan( '2018-04-20', '2018-04-21' ), Constants::STATE_AVAILABLE, [ 'granularity' => Constants::GL_DAILY ] ) );
		$this->assertStateData($room, 2018, 04, [
			'd19' => Constants::STATE_AVAILABLE,
			'd20' => Constants::STATE_AVAILABLE,
			'd21' => Constants::STATE_AVAILABLE,
			'd22' => Constants::STATE_BOOKING,
			'd23' => Constants::STATE_BOOKING,
		]);
		abrs_db_transaction( 'rollback' );

		// Only days.
		abrs_db_transaction( 'start' );
		$this->assertTrue( abrs_apply_room_state( $room, abrs_timespan( '2018-04-09', '2018-04-15' ), Constants::STATE_UNAVAILABLE, [
			'only_days'   => [1, 3, 5, 6],
			'granularity' => Constants::GL_DAILY,
		]));

		$this->assertStateData($room, 2018, 04, [
			'd9' => Constants::STATE_UNAVAILABLE,
			'd10' => Constants::STATE_AVAILABLE,
			'd11' => Constants::STATE_UNAVAILABLE,
			'd12' => Constants::STATE_AVAILABLE,
			'd13' => Constants::STATE_UNAVAILABLE,
			'd14' => Constants::STATE_UNAVAILABLE,
			'd15' => Constants::STATE_AVAILABLE,
		]);
		abrs_db_transaction( 'rollback' );
	}

	protected function setupLuxuryRoomType() {
		$luxury = (new Room_Type)->fill([
			'title'             => 'Luxury',
			'status'            => 'publish',
			'rack_rate'         => 150,
			'maximum_occupancy' => 10,
			'number_adults'     => 2,
			'number_children'   => 2,
		]);

		$luxury->save();

		for ( $i = 0; $i < 3; $i++ ) {
			$luxury_room              = new Room;
			$luxury_room['name']      = 'Luxury - 10' . $i;
			$luxury_room['room_type'] = $luxury->get_id();
			$luxury_room->save();
		}

		return new Room_Type( $luxury->get_id() );
	}

	protected function assertStateData( $unit_id, $year, $month, $days ) {
		global $wpdb;

		$results = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}awebooking_availability` WHERE `room_id` = {$unit_id} AND `year` = '{$year}' AND `month` = '{$month}' LIMIT 1", ARRAY_A);

		foreach ( $days as $key => $value) {
			$this->assertEquals($results[$key], $value);
		}
	}

	protected function insertStateData( $room_id, $year, $month, $days ) {
		global $wpdb;

		$wpdb->insert( $wpdb->prefix . 'awebooking_availability', array_merge( $days, [
			'room_id' => $room_id,
			'year'    => $year,
			'month'   => $month,
		]));
	}

	protected function debugStateTable() {
		global $wpdb;

		$results = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}awebooking_availability`", ARRAY_A);

		dump( $results );
	}
}
