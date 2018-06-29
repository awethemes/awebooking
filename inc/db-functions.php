<?php

use AweBooking\Model\Model;

/**
 * Run a MySQL transaction query, if supported.
 *
 * @param  string $type The transaction type, start (default), commit, rollback.
 * @return void
 */
function abrs_db_transaction( $type = 'start' ) {
	global $wpdb;

	// Hide the errros before perform the action.
	$wpdb->hide_errors();

	switch ( $type ) {
		case 'commit':
			$wpdb->query( 'COMMIT' );
			break;
		case 'rollback':
			$wpdb->query( 'ROLLBACK' );
			break;
		default:
			$wpdb->query( 'START TRANSACTION' );
			break;
	}
}

/**
 * Get a room data by ID in database.
 *
 * @param  int $room The room ID.
 * @return array|null
 */
function abrs_db_room( $room ) {
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
function abrs_db_rooms_in( $room_type ) {
	$room_type = Model::parse_object_id( $room_type );

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
		$fresh_rooms = $wpdb->get_results( sprintf( "SELECT * FROM `{$wpdb->prefix}awebooking_rooms` WHERE `room_type` IN (%s) ORDER BY `order` ASC LIMIT 1000", join( ',', $non_cached_ids ) ), ARRAY_A );

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

	foreach ( $group_rooms as $room_type => $rooms ) {
		/* @var \AweBooking\Support\Collection $rooms */
		wp_cache_add( (int) $room_type, $rooms->all(), 'awebooking_rooms' );

		foreach ( $rooms as $item ) {
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
function abrs_clean_room_cache( $room ) {
	wp_cache_delete( $room, 'awebooking_db_room' );

	do_action( 'abrs_clean_room_cache', $room );
}

/**
 * Query customers and return customer IDs.
 *
 * @param  string $term  The search term.
 * @param  int    $limit Limit the search results.
 * @return array
 */
function abrs_search_customers( $term, $limit = 0 ) {
	// Apply fillter to allow users custom the results.
	$results = apply_filters( 'abrs_pre_search_customers', false, $term, $limit );

	// If custom search results available, just return it.
	if ( is_array( $results ) ) {
		return $results;
	}

	$query = new WP_User_Query( apply_filters( 'abrs_customer_search_query', [
		'fields'         => 'ID',
		'number'         => $limit,
		'search'         => '*' . esc_attr( $term ) . '*',
		'search_columns' => [ 'user_login', 'user_url', 'user_email', 'user_nicename', 'display_name' ],
	], $term, $limit, 'main_query' ) );

	$query2 = new WP_User_Query( apply_filters( 'abrs_customer_search_query', [
		'fields'         => 'ID',
		'number'         => $limit,
		'meta_query'     => [
			'relation' => 'OR',
			[
				'key'     => 'first_name',
				'value'   => $term,
				'compare' => 'LIKE',
			],
			[
				'key'     => 'last_name',
				'value'   => $term,
				'compare' => 'LIKE',
			],
		],
	], $term, $limit, 'meta_query' ) );

	// Merge the both results.
	$results = wp_parse_id_list(
		array_merge( (array) $query->get_results(), (array) $query2->get_results() )
	);

	// Limit the results.
	if ( $limit && count( $results ) > $limit ) {
		$results = array_slice( $results, 0, $limit );
	}

	return $results;
}

/**
 * Delete expired transients.
 *
 * @see wc_delete_expired_transients()
 *
 * @return int
 */
function abrs_delete_expired_transients() {
	global $wpdb;

	$sql = "DELETE a, b FROM $wpdb->options a, $wpdb->options b
		WHERE a.option_name LIKE %s
		AND a.option_name NOT LIKE %s
		AND b.option_name = CONCAT( '_transient_timeout_', SUBSTRING( a.option_name, 12 ) )
		AND b.option_value < %d";
	$rows = $wpdb->query( $wpdb->prepare( $sql, $wpdb->esc_like( '_transient_' ) . '%', $wpdb->esc_like( '_transient_timeout_' ) . '%', time() ) ); // WPCS: unprepared SQL ok.

	$sql = "DELETE a, b FROM $wpdb->options a, $wpdb->options b
		WHERE a.option_name LIKE %s
		AND a.option_name NOT LIKE %s
		AND b.option_name = CONCAT( '_site_transient_timeout_', SUBSTRING( a.option_name, 17 ) )
		AND b.option_value < %d";
	$rows2 = $wpdb->query( $wpdb->prepare( $sql, $wpdb->esc_like( '_site_transient_' ) . '%', $wpdb->esc_like( '_site_transient_timeout_' ) . '%', time() ) ); // WPCS: unprepared SQL ok.

	return absint( $rows + $rows2 );
}
add_action( 'awebooking_installed', 'abrs_delete_expired_transients' );
