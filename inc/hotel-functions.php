<?php

use AweBooking\Constants;
use AweBooking\Model\Room;
use AweBooking\Model\Hotel;
use AweBooking\Model\Service;
use AweBooking\Model\Room_Type;
use AweBooking\Model\Pricing\Base_Rate;
use AweBooking\Model\Pricing\Base_Single_Rate;
use AweBooking\Model\Pricing\Contracts\Rate;
use AweBooking\Model\Pricing\Contracts\Single_Rate;

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
 * Gets the base rate by a room type.
 *
 * @param  \AweBooking\Model\Room_Type|int $room_type The room type ID.
 * @return \AweBooking\Model\Pricing\Base_Rate|null
 */
function abrs_get_base_rate( $room_type ) {
	return ( $room_type = abrs_get_room_type( $room_type ) )
		? new Base_Rate( $room_type )
		: null;
}

/**
 * Gets the base rate by a room type.
 *
 * @param  \AweBooking\Model\Room_Type|int $room_type The room type ID.
 * @return \AweBooking\Model\Pricing\Base_Single_Rate|null
 */
function abrs_get_base_single_rate( $room_type ) {
	return ( $room_type = abrs_get_room_type( $room_type ) )
		? new Base_Single_Rate( $room_type )
		: null;
}

/**
 * Retrieves the rate object.
 *
 * Just a placeholder function for pro version :).
 *
 * @param  mixed $rate The rate ID.
 * @return \AweBooking\Model\Pricing\Contracts\Rate|null
 */
function abrs_get_rate( $rate ) {
	return $rate instanceof Base_Rate ? $rate
		: apply_filters( 'abrs_get_rate_object', null, $rate );
}

/**
 * Retrieves the single_rate object.
 *
 * Just a placeholder function for pro version.
 *
 * @param  mixed $single_rate The single_rate ID.
 * @return \AweBooking\Model\Pricing\Contracts\Single_Rate|null
 */
function abrs_get_single_rate( $single_rate ) {
	return ( $single_rate instanceof Base_Single_Rate ) ? $single_rate
		: apply_filters( 'abrs_get_single_rate_object', null, $single_rate );
}

/**
 * Query rates in a room type.
 *
 * Just a placeholder function for pro version.
 *
 * @param  \AweBooking\Model\Room_Type|int $room_type The room type ID.
 * @return \AweBooking\Support\Collection
 */
function abrs_query_rates( $room_type ) {
	return abrs_collect( apply_filters( 'abrs_query_rates', [], $room_type ) )
		->filter( function ( $rate ) {
			return $rate instanceof Rate;
		})->sortBy( function( Rate $rate ) {
			return $rate->get_priority();
		})->values();
}

/**
 * Query single rates inside rate.
 *
 * Just a placeholder function for pro version :).
 *
 * @param \AweBooking\Model\Pricing\Contracts\Rate|int $rate The rate belong to room type.
 * @return \AweBooking\Support\Collection
 */
function abrs_query_single_rates( $rate ) {
	return abrs_collect( apply_filters( 'abrs_query_single_rates', [], $rate ) )
		->filter( function ( $plan ) {
			return $plan instanceof Single_Rate;
		})->sortBy( function ( Single_Rate $rate ) {
			return $rate->get_priority();
		})->values();
}

/**
 * Retrieves the hotel object.
 *
 * @param  mixed $hotel The hotel ID.
 * @return \AweBooking\Model\Hotel|false|null
 */
function abrs_get_hotel( $hotel = 0 ) {
	if ( 0 == $hotel ) {
		return abrs_get_primary_hotel();
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
function abrs_get_primary_hotel() {
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
 * @param array $args         Optional, the WP_Query args.
 * @param bool  $with_primary Return with primary hotel?.
 * @return \AweBooking\Support\Collection
 */
function abrs_list_hotels( $args = [], $with_primary = false ) {
	$args = wp_parse_args( $args, apply_filters( 'abrs_query_hotels_args', [
		'post_type'      => Constants::HOTEL_LOCATION,
		'post_status'    => 'publish',
		'posts_per_page' => 500, // Limit max 500.
		'order'          => 'ASC',
		'orderby'        => 'menu_order',
	]));

	$wp_query = new WP_Query( $args );

	$hotels = abrs_collect( $wp_query->posts )
		->map_into( Hotel::class );

	if ( $with_primary ) {
		$hotels = $hotels->prepend( abrs_get_primary_hotel() );
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

/**
 * Gets all services.
 *
 * @param  array $args Optional, the WP_Query args.
 * @return \AweBooking\Support\Collection
 */
function abrs_list_services( $args = [] ) {
	$args = wp_parse_args( $args, apply_filters( 'abrs_query_services_args', [
		'post_type'      => Constants::HOTEL_SERVICE,
		'post_status'    => 'publish',
		'posts_per_page' => 500, // Limit max 500.
		'order'          => 'ASC',
		'orderby'        => 'menu_order',
	]));

	$wp_query = new WP_Query( $args );

	$services = abrs_collect( $wp_query->posts )
		->map_into( Service::class );

	return $services;
}

/**
 * Get room beds.
 *
 * // TODO: ...
 *
 * @param  int    $room_type The room type.
 * @param  string $separator The separator.
 * @return string
 */
function abrs_get_room_beds( $room_type, $separator = ', ' ) {
	$room_type = abrs_get_room_type( $room_type );

	if ( ! $room_type ) {
		return '';
	}

	$beds = $room_type->get( 'beds' );

	$items = [];
	foreach ( $beds as $bed ) {
		/* translators: %1$s number of beds, %2$s bed type */
		$items[] = sprintf( __( '<span>%1$s %2$s</span>', 'awebooking' ), absint( $bed['number'] ), $bed['type'] );
	}

	return implode( $items, $separator );
}
