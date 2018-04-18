<?php

use AweBooking\Model\Room;
use AweBooking\Model\Room_Type;
use AweBooking\Model\Model;

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
 * @return array
 */
function abrs_get_rooms( $room_type ) {
	$room_type = Model::parse_object_id( $room_type );

	// Because room type is just is a post type, so
	// ensure this post exists before doing anything.
	if ( ! get_post( $room_type ) ) {
		return;
	}

	// If current site running on multilanguage, we will get room-units
	// from original room_type. To avoid the "replication" have own room-units.
	if ( abrs_running_on_multilanguage() ) {
		$room_type = awebooking( 'multilingual' )->get_original_post( $room_type );
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
 * Will clean the room in the cache.
 *
 * @param  \AweBooking\Model\Room|int $room The room ID or room model.
 * @return void
 */
function abrs_clean_room_cache( $room ) {
	// Leave if given room is not exists.
	if ( ! $room = abrs_db_room( $room ) ) {
		return;
	}

	wp_cache_delete( (int) $room['id'], 'awebooking_db_room' );
	wp_cache_delete( (int) $room['room_type'], 'awebooking_rooms' );

	// Fire action after cache cleaned.
	do_action( 'awebooking/clean_room_cache', $room );
}

/**
 * Get a booking item by ID in database.
 *
 * @param  int    $item The booking item ID.
 * @param  string $type Get only matching booking item type.
 * @return array|null
 */
function abrs_db_booking_item( $item, $type = null ) {
	// Try to get the item in cache first, otherwise load from database.
	$db_item = wp_cache_get( $item, 'awebooking_db_booking_item' );

	if ( false === $db_item ) {
		global $wpdb;

		$where = $type ? ' AND booking_item_type = "' . esc_sql( $type ) . '"' : '';
		// @codingStandardsIgnoreLine
		$db_item = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}awebooking_booking_items WHERE booking_item_id = %d " . $where . ' LIMIT 1', $item ), ARRAY_A );

		wp_cache_add( (int) $db_item['booking_item_id'], $db_item, 'awebooking_db_booking_item' );
	}

	return $db_item;
}

/**
 * Get the booking items by given a type.
 *
 * @param  int    $booking The booking ID.
 * @param  string $type    Optional, filter only item type.
 * @return array
 */
function abrs_get_booking_items( $booking, $type = 'all' ) {
	$booking = Model::parse_object_id( $booking );

	// Ensure this booking exists before doing anything.
	if ( ! get_post( $booking ) ) {
		return;
	}

	// Try to get the items in cache first, otherwise load from database.
	$items = wp_cache_get( $booking, 'awebooking_booking_items' );

	if ( false === $items ) {
		global $wpdb;

		$items = $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}awebooking_booking_items` WHERE `booking_id` = %d ORDER BY `booking_item_id`", $booking ), ARRAY_A
		);

		// Cache each item in results.
		foreach ( $items as $item ) {
			wp_cache_set( (int) $item['booking_item_id'], $item, 'awebooking_db_booking_item' );
		}

		// Add this results to cache.
		wp_cache_add( $booking, $items, 'awebooking_booking_items' );
	}

	// Filter correct the item type to return.
	if ( 'all' !== $type ) {
		$items = wp_list_filter( $items, [ 'booking_item_type' => $type ] );
	}

	return $items;
}

/**
 * Will clean the booking item in the cache.
 *
 * @param  \AweBooking\Model\Booking\Item|int $item The booking item ID or booking item model.
 * @return void
 */
function abrs_clean_booking_item_cache( $item ) {
	// Leave if given item is not exists.
	if ( ! $item = abrs_db_booking_item( $item ) ) {
		return;
	}

	wp_cache_delete( (int) $item['booking_item_id'], 'awebooking_db_booking_item' );
	wp_cache_delete( (int) $item['booking_item_id'], 'booking_itemmeta_meta' );
	wp_cache_delete( (int) $item['booking_id'], 'awebooking_booking_items' );

	// Fire action after cache cleaned.
	do_action( 'awebooking/clean_booking_item_cache', $item );
}

/**
 * Get all bookings have room id.
 *
 * @param  int $room_id Room id
 * @return array
 */
function abrs_get_bookings_by_room( $room_id, $booking_status = [] ) {
	global $wpdb;

	$query =  "SELECT `posts`.`ID` FROM {$wpdb->posts} AS posts ";
	$query .= "INNER JOIN `{$wpdb->prefix}awebooking_booking_items` AS `booking_item` ON (posts.post_type = 'awebooking' AND `posts`.`ID` = `booking_item`.`booking_id`) ";

	if ( $booking_status ) {
		$query .= "AND posts.post_status IN ( '" . implode( "', '", $booking_status ) . "') ";
	}

	$query .= "INNER JOIN {$wpdb->prefix}awebooking_booking_itemmeta AS itemmeta ON (`booking_item`.`booking_item_id` = `itemmeta`.`booking_item_id` AND `itemmeta`.`meta_key` = '_room_id' ) ";
	$query .= "WHERE `itemmeta`.`meta_value` = '{$room_id}' ";
	$query .= "ORDER BY `posts`.`post_date` DESC";

	$results = $wpdb->get_results( $query );

	return array_map( 'absint', wp_list_pluck( $results, 'ID' ) );
}

/**
 * Search customers and return customer IDs.
 *
 * @param  string $term  The search term.
 * @param  int    $limit Limit the search results.
 * @return array
 */
function abrs_search_customers( $term, $limit = 0 ) {
	// Apply fillter to allow users custom the results.
	$results = apply_filters( 'awebooking/pre_search_customers', false, $term, $limit );

	// If custom search results available, just return it.
	if ( is_array( $results ) ) {
		return $results;
	}

	$query = new WP_User_Query( apply_filters( 'awebooking/customer_search_query', [
		'fields'         => 'ID',
		'number'         => $limit,
		'search'         => '*' . esc_attr( $term ) . '*',
		'search_columns' => [ 'user_login', 'user_url', 'user_email', 'user_nicename', 'display_name' ],
	], $term, $limit, 'main_query' ) );

	$query2 = new WP_User_Query( apply_filters( 'awebooking/customer_search_query', [
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
