<?php

use AweBooking\Constants;
use AweBooking\Model\Room;
use AweBooking\Model\Room_Type;
use AweBooking\Model\Hotel;
use AweBooking\Model\Service;
use AweBooking\Model\Pricing\Base_Rate;
use AweBooking\Model\Pricing\Standard_Plan;
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
	if ( $rate instanceof Base_Rate ) {
		return $rate;
	}

	// Let's check given rate if it is base rate or not.
	$base_rate = abrs_get_room_type(
		Model::parse_object_id( $rate )
	);

	return ( $base_rate instanceof Room_Type )
		? new Base_Rate( $base_rate )
		: apply_filters( 'awebooking/get_rate_object', null, $rate );
}

/**
 * Retrieves the rate plan object.
 *
 * @param  mixed $rate_plan The rate plan ID.
 * @return \AweBooking\Model\Pricing\Rate_Plan|null
 */
function abrs_get_rate_plan( $rate_plan ) {
	if ( $rate_plan instanceof Standard_Plan ) {
		return $rate_plan;
	}

	// Let's check given rate_plan if it is standard rate or not.
	$standard_plan = abrs_get_room_type(
		Model::parse_object_id( $rate_plan )
	);

	return ( $standard_plan instanceof Room_Type )
		? new Standard_Plan( $standard_plan )
		: apply_filters( 'awebooking/get_rate_plan_object', null, $rate_plan );
}

/**
 * Retrieves the hotel object.
 *
 * @param  mixed $hotel The post object or post ID of the hotel.
 * @return \AweBooking\Model\Hotel|false|null
 */
function abrs_get_hotel( $hotel ) {
	if ( 0 == $hotel ) {
		return abrs_get_default_hotel();
	}

	return abrs_rescue( function() use ( $hotel ) {
		$hotel = new Hotel( $hotel );

		return $hotel->exists() ? $hotel : null;
	}, false );
}

/**
 * Returns the default hotel.
 *
 * @return \AweBooking\Model\Hotel
 */
function abrs_get_default_hotel() {
	if ( ! awebooking()->bound( 'default_hotel' ) ) {
		awebooking()->singleton( 'default_hotel', function () {
			return new Hotel( 'default' );
		});
	}

	return awebooking()->make( 'default_hotel' );
}

/**
 * Gets all hotels.
 *
 * @param  array $args Optional, the WP_Query args.
 * @return \AweBooking\Support\Collection
 */
function abrs_list_hotels( $args = [], $with_default = false ) {
	$args = wp_parse_args( $args, apply_filters( 'awebooking/query_hotels_args', [
		'post_type'      => Constants::HOTEL_LOCATION,
		'post_status'    => 'publish',
		'posts_per_page' => 500, // Limit max 500.
		'order'          => 'ASC',
		'orderby'        => 'menu_order',
	]));

	$wp_query = new WP_Query( $args );

	$hotels = abrs_collect( $wp_query->posts )
		->map_into( Hotel::class );

	if ( $with_default ) {
		$hotels = $hotels->prepend( abrs_get_default_hotel() );
	}

	return $hotels;
}

/**
 * Retrieves the service object.
 *
 * @param  mixed $service The post object or post ID of the service.
 * @return \AweBooking\Model\Service|false|null
 */
function abrs_get_service( $service ) {
	return abrs_rescue( function() use ( $service ) {
		$service = new Service( $service );

		return $service->exists() ? $service : null;
	}, false );
}
