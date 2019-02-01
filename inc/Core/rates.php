<?php

use AweBooking\Constants;
use AweBooking\Model\Pricing\Base_Rate;
use AweBooking\Model\Pricing\Contracts\Rate;
use AweBooking\Model\Pricing\Standard_Rate_Interval;
use AweBooking\Model\Pricing\Contracts\Rate_Interval;

/**
 * Retrieves the rate object.
 *
 * Just a placeholder function for pro version :).
 *
 * @param  mixed $rate The rate ID.
 * @return \AweBooking\Model\Pricing\Contracts\Rate|null
 */
function abrs_get_rate( $rate ) {
	// Trying get base rate.
	if ( $_base_rate = abrs_get_base_rate( $rate ) ) {
		$rate = $_base_rate;
	}

	return $rate instanceof Base_Rate ? $rate
		: apply_filters( 'abrs_get_rate_object', null, $rate );
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
		})->sortByDesc( function( Rate $rate ) {
			return $rate->get_priority();
		})->values();
}

/**
 * Retrieves the rate interval object.
 *
 * Just a placeholder function for pro version.
 *
 * @param  mixed $rate The rate interval ID.
 * @return \AweBooking\Model\Pricing\Contracts\Rate_Interval|null
 */
function abrs_get_rate_interval( $rate ) {
	if ( is_int( $rate ) && Constants::ROOM_TYPE === get_post_type( $rate ) ) {
		$rate = abrs_get_standard_rate_interval( $rate );
	}

	return ( $rate instanceof Standard_Rate_Interval ) ? $rate
		: apply_filters( 'abrs_get_rate_interval_object', null, $rate );
}

/**
 * Gets all rate intervals in a rate.
 *
 * Just a placeholder function for pro version :).
 *
 * @param \AweBooking\Model\Pricing\Contracts\Rate|int $rate The rate belong to room type.
 * @return \AweBooking\Support\Collection
 */
function abrs_get_rate_intervals( $rate ) {
	return abrs_collect( apply_filters( 'abrs_get_rate_intervals', [], $rate ) )
		->filter( function ( $plan ) {
			return $plan instanceof Rate_Interval;
		} )->sortByDesc( function ( Rate_Interval $rate ) {
			return $rate->get_priority();
		} )->values();
}

/**
 * Gets the base rate by a room type.
 *
 * @param  \AweBooking\Model\Room_Type|int $room_type The room type ID.
 *
 * @return \AweBooking\Model\Pricing\Standard_Rate_Interval|null
 */
function abrs_get_standard_rate_interval( $room_type ) {
	return ( $room_type = abrs_get_room_type( $room_type ) )
		? new Standard_Rate_Interval( $room_type )
		: null;
}
