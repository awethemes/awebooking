<?php

use AweBooking\Model\Room;
use AweBooking\Model\Room_Type;

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
 * Get a room data by ID in database.
 *
 * @param  int $room The room ID.
 * @return array|null
 */
function abrs_get_raw_room( $room ) {
	// Try to get the room in cache first, otherwise load from database.
	$room_unit = wp_cache_get( $room, 'awebooking_db_room' );

	if ( false === $room_unit ) {
		global $wpdb;

		$room_unit = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}awebooking_rooms` WHERE `id` = %d LIMIT 1", $room ), ARRAY_A );

		wp_cache_add( (int) $room_unit['id'], $room_unit, 'awebooking_db_room' );
	}

	return $room_unit;
}

/**
 * Get all room items in a room type.
 *
 * @param  int $room_type The room type ID.
 * @return array|null
 */
function abrs_get_raw_rooms( $room_type ) {
	$room_type = abrs_parse_object_id( $room_type );

	// Because room type is just is a post type, so
	// ensure this post exists before doing anything.
	if ( ! get_post( $room_type ) ) {
		return null;
	}

	// If current site running on multilanguage, we will get room-units
	// from original room_type. To avoid the "replication" have own room-units.
	if ( abrs_running_on_multilanguage() ) {
		$room_type = abrs_multilingual()->get_original_post( $room_type );
	}

	// Try to get the rooms in cache first, otherwise load from database.
	$rooms = wp_cache_get( $room_type, 'awebooking_rooms' );

	if ( false === $rooms ) {
		global $wpdb;

		$rooms = $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}awebooking_rooms` WHERE `room_type` = %d ORDER BY `order` ASC LIMIT 1000", $room_type ), ARRAY_A
		);

		// Cache each item in results.
		foreach ( $rooms as $item ) {
			wp_cache_set( (int) $item['id'], $item, 'awebooking_db_room' );
		}

		// Add this results to cache.
		wp_cache_add( $room_type, $rooms, 'awebooking_rooms' );
	}

	return $rooms;
}

/**
 * Adds any rooms from the given ids to the cache that do not already exist in the cache.
 *
 * @param  array $ids ID list.
 * @return void
 */
function abrs_prime_room_caches( $ids ) {
	global $wpdb;

	$non_cached_ids = _get_non_cached_ids( (array) $ids, 'awebooking_rooms' );

	if ( ! empty( $non_cached_ids ) ) {
		// @codingStandardsIgnoreLine
		$fresh_rooms = $wpdb->get_results( sprintf( "SELECT * FROM `{$wpdb->prefix}awebooking_rooms` WHERE `room_type` IN (%s) ORDER BY `order` ASC LIMIT 1000", implode( ',', $non_cached_ids ) ), ARRAY_A );

		abrs_update_room_caches( $fresh_rooms );
	}
}

/**
 * Call major cache updating functions for list of rooms.
 *
 * @param  array $rooms Array of rooms.
 * @return void
 */
function abrs_update_room_caches( array $rooms ) {
	if ( empty( $rooms ) ) {
		return;
	}

	$group_rooms = abrs_collect( $rooms )->groupBy( 'room_type' );

	foreach ( $group_rooms as $room_type => $_rooms ) {
		/* @var \AweBooking\Support\Collection $_rooms */
		wp_cache_add( (int) $room_type, $_rooms->all(), 'awebooking_rooms' );

		foreach ( $_rooms as $item ) {
			wp_cache_set( (int) $item['id'], $item, 'awebooking_db_room' );
		}
	}
}

/**
 * Will clean the room in the cache.
 *
 * @param  \AweBooking\Model\Room|int $room The room ID or room model.
 * @return void
 */
function abrs_flush_room_cache( $room ) {
	wp_cache_delete( $room, 'awebooking_db_room' );

	do_action( 'abrs_clean_room_cache', $room );
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
