<?php

use AweBooking\Constants;
use AweBooking\Model\Hotel;

/**
 * Retrieves the hotel object.
 *
 * @param  mixed $hotel The hotel ID.
 * @return \AweBooking\Model\Hotel|false|null
 */
function abrs_get_hotel( $hotel = 0 ) {
	if ( is_int( $hotel ) && 0 === (int) $hotel ) {
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
	if ( abrs_multiple_hotels() ) {
		return new Hotel( abrs_get_page_id( 'primary_hotel' ) );
	}

	return new Hotel( 'default' );
}

/**
 * Gets all hotels.
 *
 * @param array $args         Optional, the WP_Query args.
 * @return \AweBooking\Support\Collection
 */
function abrs_list_hotels( $args = [] ) {
	$args = wp_parse_args( $args, apply_filters( 'abrs_query_hotels_args', [
		'post_type'      => Constants::HOTEL_LOCATION,
		'post_status'    => 'publish',
		'posts_per_page' => 500, // Limit max 500.
		'order'          => 'ASC',
		'orderby'        => 'menu_order',
	] ) );

	$wp_query = new WP_Query( $args );

	return abrs_collect( $wp_query->posts )
		->map_into( Hotel::class );
}

/**
 * Returns un-mapped room types ids (used in settings).
 *
 * @param  int $limit Max number of room types. Defaults to -1 (no limit).
 * @return array
 */
function abrs_get_orphan_room_types( $limit = -1 ) {
	return get_posts( [
		'post_type'      => Constants::ROOM_TYPE,
		'post_status'    => 'any',
		'fields'         => 'ids',
		'posts_per_page' => $limit,
		'nopaging'       => $limit <= 0,
		'meta_query' => [
			[
				'key'     => '_hotel_id',
				'value'   => wp_list_pluck( get_pages( [ 'post_type' => Constants::HOTEL_LOCATION ] ), 'ID' ),
				'compare' => 'NOT IN',
			],
		],
	] );
}

/**
 * Reuturns WP_Query of room types by hotel.
 *
 * @param int $hotel_id The hotel ID.
 *
 * @return \WP_Query
 */
function abrs_get_room_types_by_hotel( $hotel_id ) {
	return new WP_Query( [
		'post_type'      => Constants::ROOM_TYPE,
		'post_status'    => 'publish',
		'posts_per_page' => - 1,
		'meta_query'     => [
			[
				'key'     => '_hotel_id',
				'value'   => $hotel_id,
				'compare' => '=',
			],
		],
	] );
}
