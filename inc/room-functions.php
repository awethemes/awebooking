<?php

use AweBooking\Model\Room;
use AweBooking\Model\Room_Type;
use AweBooking\Model\Pricing\Base_Rate;
use AweBooking\Model\Model;

/**
 * Retrieves the room object.
 *
 * @param  mixed $room The room ID.
 * @return \AweBooking\Model\Room|false|null
 */
function abrs_get_room( $room ) {
	return abrs_rescue( function() use ( $room ) {
		$room = new Room( $room );

		return $room->exists() ? $room : null;
	}, false );
}

/**
 * Retrieves the room type object.
 *
 * @param  mixed $room_type The post object or post ID of the room type.
 * @return \AweBooking\Model\Room_Type|false|null
 */
function abrs_get_room_type( $room_type ) {
	return abrs_rescue( function() use ( $room_type ) {
		$room_type = new Room_Type( $room_type );

		return $room_type->exists() ? $room_type : null;
	}, false );
}

/**
 * Retrieves the rate object.
 *
 * In awebooking, we merge rate & rate plan along with room type.
 * So each room type alway have a "Base Rate" that same ID with room type.
 *
 * @param  mixed $rate The rate ID.
 * @return \AweBooking\Model\Pricing\Rate|null
 */
function abrs_get_rate( $rate ) {
	$rate = Model::parse_object_id( $rate );

	// Let's check given rate if it is base rate or not.
	$base_rate = abrs_get_room_type( $rate );

	return ( $base_rate instanceof Room_Type )
		? new Base_Rate( $base_rate )
		: apply_filters( 'awebooking/get_rate_object', null, $rate );
}