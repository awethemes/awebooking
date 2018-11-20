<?php

use AweBooking\Constants;
use AweBooking\Model\Booking;
use AweBooking\Model\Booking\Item;
use AweBooking\Model\Booking\Note as Booking_Note;
use AweBooking\Model\Booking\Payment_Item;
use Illuminate\Support\Arr;

/**
 * Retrieves the booking object.
 *
 * @param  mixed $booking The post object or post ID of the booking.
 * @return \AweBooking\Model\Booking|false|null
 */
function abrs_get_booking( $booking ) {
	if ( $booking instanceof Booking && $booking->exists() ) {
		return $booking;
	}

	return abrs_rescue( function() use ( $booking ) {
		$booking = new Booking( $booking );

		return $booking->exists() ? $booking : null;
	}, false );
}

/**
 * Returns a list of booking statuses.
 *
 * @return array
 */
function abrs_get_booking_statuses() {
	return apply_filters( 'abrs_list_booking_statuses', [
		'awebooking-pending'     => _x( 'Pending', 'Booking status', 'awebooking' ),
		'awebooking-inprocess'   => _x( 'Processing', 'Booking status', 'awebooking' ),
		'awebooking-on-hold'     => _x( 'Reserved', 'Booking status', 'awebooking' ),
		'awebooking-deposit'     => _x( 'Deposit', 'Booking status', 'awebooking' ),
		'awebooking-completed'   => _x( 'Paid', 'Booking status', 'awebooking' ),
		'checked-in'             => _x( 'Checked In', 'Booking status', 'awebooking' ),
		'checked-out'            => _x( 'Checked Out', 'Booking status', 'awebooking' ),
		'awebooking-cancelled'   => _x( 'Cancelled', 'Booking status', 'awebooking' ),
	]);
}

/**
 * Get the nice name for an booking status.
 *
 * @param  string $status The status name.
 * @return string
 */
function abrs_get_booking_status_name( $status ) {
	$statuses = abrs_get_booking_statuses();

	$status = ( 0 === strpos( $status, 'awebooking-' ) ) ? substr( $status, 11 ) : $status;

	if ( array_key_exists( $status, $statuses ) ) {
		return $statuses[ $status ];
	}

	return Arr::get( $statuses, 'awebooking-' . $status, $status );
}

/**
 * Apply prefix 'awebooking-' into given booking status.
 *
 * @param  string $status The booking  status.
 * @return string
 */
function abrs_prefix_booking_status( $status ) {
	// No need to prefix.
	if ( in_array( $status, [ 'checked-in', 'checked-out', 'trash' ] ) ) {
		return $status;
	}

	return ( false === strpos( $status, 'awebooking-' ) )
		? 'awebooking-' . $status
		: $status;
}

/**
 * Retrieves the booking item.
 *
 * @param  mixed $item The item ID or item array.
 * @return \AweBooking\Model\Booking\Item|false|null
 */
function abrs_get_booking_item( $item ) {
	// Given a numeric, let's get item from DB.
	if ( is_numeric( $item ) ) {
		$item = abrs_get_raw_booking_item( $item );
	}

	// Try to resolve the item type.
	if ( $item instanceof Item ) {
		$item_id   = $item->get_id();
		$item_type = $item->get_type();
	} elseif ( is_array( $item ) && ! empty( $item['booking_item_type'] ) ) {
		$item_id   = $item['booking_item_id'];
		$item_type = $item['booking_item_type'];
	}

	// If can't resolve the item type, just leave.
	if ( ! isset( $item_id, $item_type ) ) {
		return false;
	}

	$classmap = abrs_booking_item_classmap();
	if ( ! array_key_exists( $item_type, $classmap ) ) {
		return false;
	}

	// Apply filters allow users can overwrite the class name.
	$classname = apply_filters( 'abrs_get_booking_item_classname', $classmap[ $item_type ], $item_type, $item_id );

	return abrs_rescue( function() use ( $classname, $item_id ) {
		$item = new $classname( $item_id );

		if ( ! $item instanceof Item ) {
			return false;
		}

		return $item->exists() ? $item : null;
	}, false );
}

/**
 * Delete a booking item.
 *
 * @param  mixed $item The item ID or item array.
 * @return boolean
 */
function abrs_delete_booking_item( $item ) {
	if ( ! $item = abrs_get_booking_item( $item ) ) {
		return false;
	}

	// Get the booking reference.
	$rel = abrs_get_booking( $item->get( 'booking_id' ) );

	// Delete the booking item.
	$deleted = $item->delete();

	// Recalculate totals of booking.
	if ( $rel ) {
		$rel->flush_items();
		$rel->calculate_totals();
	}

	return $deleted;
}

/**
 * Returns an array of booking item classmap.
 *
 * @return array
 */
function abrs_booking_item_classmap() {
	return apply_filters( 'abrs_booking_items_classmap', [
		'line_item'    => \AweBooking\Model\Booking\Room_Item::class,
		'payment_item' => \AweBooking\Model\Booking\Payment_Item::class,
		'service_item' => \AweBooking\Model\Booking\Service_Item::class,
		'fee_item'     => \AweBooking\Model\Booking\Fee_Item::class,
		'tax_item'     => \AweBooking\Model\Booking\Tax_Item::class,
	]);
}

/**
 * Get a booking item by ID in database.
 *
 * @param  int    $item The booking item ID.
 * @param  string $type Get only matching booking item type.
 * @return array|null
 */
function abrs_get_raw_booking_item( $item, $type = null ) {
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
 * @return array|null
 */
function abrs_get_raw_booking_items( $booking, $type = 'all' ) {
	$booking = abrs_parse_object_id( $booking );

	// Ensure this booking exists before doing anything.
	if ( ! get_post( $booking ) ) {
		return null;
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
 * Gets the last payment item in a booking.
 *
 * @param  \AweBooking\Model\Booking|int $booking The booking ID.
 * @return Payment_Item|WP_Error|null
 */
function abrs_get_last_booking_payment( $booking ) {
	global $wpdb;

	// Returns a WP_Error if given a invalid booking.
	if ( ! get_post( $booking = abrs_parse_object_id( $booking ) ) ) {
		return new WP_Error( 'booking-error', esc_html__( 'Invalid booking ID.', 'awebooking' ) );
	}

	$db_item = $wpdb->get_row(
		$wpdb->prepare( "SELECT `booking_item_id` FROM `{$wpdb->prefix}awebooking_booking_items` WHERE `booking_id` = %d AND `booking_item_type` = 'payment_item' ORDER BY `booking_item_id` DESC LIMIT 1", $booking ), ARRAY_A
	);

	if ( ! $db_item || empty( $db_item['booking_item_id'] ) ) {
		return null;
	}

	return new Payment_Item( $db_item['booking_item_id'] );
}

/**
 * Will clean the booking item in the cache.
 *
 * @param  int $item The booking item ID or booking item model.
 * @return void
 */
function abrs_flush_booking_item_cache( $item ) {
	wp_cache_delete( $item, 'awebooking_db_booking_item' );

	wp_cache_delete( $item, 'booking_itemmeta_meta' );

	do_action( 'abrs_clean_booking_item_cache', $item );
}

/**
 * Gets a booking note.
 *
 * @param  int|WP_Comment $data Note ID (or WP_Comment instance for internal use only).
 * @return \AweBooking\Model\Booking\Note|null
 */
function abrs_get_booking_note( $data ) {
	// Try resolve the comment data.
	if ( is_numeric( $data ) ) {
		$data = get_comment( $data );
	}

	// Leave if data is not instance of WP_Comment.
	if ( ! $data instanceof WP_Comment ) {
		return null;
	}

	$booking_note = new Booking_Note([
		'id'            => (int) $data->comment_ID,
		'content'       => make_clickable( $data->comment_content ),
		'date_created'  => abrs_date_time( $data->comment_date ),
		'customer_note' => (bool) get_comment_meta( $data->comment_ID, 'is_customer_note', true ),
		'added_by'      => esc_html__( 'AweBooking', 'awebooking' ) === $data->comment_author ? 'system' : $data->comment_author,
	]);

	return apply_filters( 'abrs_get_booking_note', $booking_note, $data );
}

/**
 * Gets the booking notes.
 *
 * @param  array $args {
 *     Array of query parameters.
 *
 *     @type string $limit           Maximum number of notes to retrieve.
 *                                   Default empty (no limit).
 *     @type int    $booking_id      Limit results to those affiliated with a given booking ID.
 *                                   Default 0.
 *     @type array  $booking__in     Array of booking IDs to include affiliated notes for.
 *                                   Default empty.
 *     @type array  $booking__not_in Array of booking IDs to exclude affiliated notes for.
 *                                   Default empty.
 *     @type string $bookingby       Define how should sort notes.
 *                                   Accepts 'date_created', 'date_created_gmt' or 'id'.
 *                                   Default: 'id'.
 *     @type string $booking         How to booking retrieved notes.
 *                                   Accepts 'ASC' or 'DESC'.
 *                                   Default: 'DESC'.
 *     @type string $type            Define what type of note should retrieve.
 *                                   Accepts 'customer', 'internal' or empty for both.
 *                                   Default empty.
 * }
 * @return \AweBooking\Support\Collection
 */
function abrs_get_booking_notes( $args ) {
	$key_mapping = [
		'limit'           => 'number',
		'booking_id'      => 'post_id',
		'booking__in'     => 'post__in',
		'booking__not_in' => 'post__not_in',
	];

	$orderby_mapping = [
		'id'               => 'comment_ID',
		'date_created'     => 'comment_date',
		'date_created_gmt' => 'comment_date_gmt',
	];

	// Transform the keys.
	foreach ( $key_mapping as $query_key => $db_key ) {
		if ( isset( $args[ $query_key ] ) ) {
			$args[ $db_key ] = $args[ $query_key ];
			unset( $args[ $query_key ] );
		}
	}

	// Transform the orderby args.
	$args['orderby'] = ! empty( $args['orderby'] ) && array_key_exists( $args['orderby'], $orderby_mapping )
		? $orderby_mapping[ $args['orderby'] ]
		: 'comment_ID';

	// Set the booking note type.
	if ( isset( $args['type'] ) && 'customer' === $args['type'] ) {
		$args['meta_query'] = [[ // @codingStandardsIgnoreLine
			'key'     => 'is_customer_note',
			'value'   => 1,
			'compare' => '=',
		]]; // @codingStandardsIgnoreLine
	} elseif ( isset( $args['type'] ) && 'internal' === $args['type'] ) {
		$args['meta_query'] = [[ // @codingStandardsIgnoreLine
			'key'     => 'is_customer_note',
			'compare' => 'NOT EXISTS',
		]]; // @codingStandardsIgnoreLine
	}

	// Set correct comment type.
	$args['type'] = Constants::BOOKING_NOTE;

	// Always approved.
	$args['status'] = 'approve';

	// Does not support 'count' or 'fields'.
	unset( $args['count'], $args['fields'] );

	remove_filter( 'comments_clauses', '_abrs_exclude_booking_comments', 10 );

	$notes = get_comments( $args );

	add_filter( 'comments_clauses', '_abrs_exclude_booking_comments', 10, 1 );

	return abrs_collect( array_filter( $notes ) )
		->transform( 'abrs_get_booking_note' );
}

/**
 * Adds a note (comment) to the booking.
 *
 * @param  int    $booking           The booking ID.
 * @param  string $note              Note to add.
 * @param  false  $is_customer_note  Is this a note for the customer?.
 * @param  bool   $added_by_user     Was the note added by a user?.
 * @return int|false|WP_Error
 */
function abrs_add_booking_note( $booking, $note, $is_customer_note = false, $added_by_user = false ) {
	if ( empty( $note ) ) {
		return false;
	}

	$booking = abrs_get_booking( $booking );
	if ( ! $booking ) {
		return new WP_Error( 'invalid_booking_id', esc_html__( 'Invalid Booking ID.', 'awebooking' ), [ 'status' => 400 ] );
	}

	// In case note added by user, we will load comment data from current user.
	if ( is_user_logged_in() && $added_by_user ) {
		$user                 = get_user_by( 'id', get_current_user_id() );
		$comment_author       = $user->display_name;
		$comment_author_email = $user->user_email;
	} else {
		$host_name = abrs_rescue( function () {
			abrs_http_request()->getHttpHost();
		});

		$comment_author       = esc_html__( 'AweBooking', 'awebooking' );
		$comment_author_email = strtolower( esc_html__( 'AweBooking', 'awebooking' ) ) . '@' . ( $host_name ? str_replace( 'www.', '', $host_name ) : 'noreply.com' );
		$comment_author_email = sanitize_email( $comment_author_email );
	}

	// Prepare comment data.
	$commentdata = apply_filters( 'abrs_add_booking_note_data', [
		'comment_post_ID'      => $booking->get_id(),
		'comment_author'       => $comment_author,
		'comment_author_email' => $comment_author_email,
		'comment_author_url'   => '',
		'comment_content'      => $note,
		'comment_agent'        => 'AweBooking',
		'comment_type'         => Constants::BOOKING_NOTE,
		'comment_parent'       => 0,
		'comment_approved'     => 1,
	], $is_customer_note, $added_by_user );

	// Inserts comment into the database.
	$comment_id = wp_insert_comment( $commentdata );

	// In case this note for customer, we will fire a action
	// to other can be hooks into this, notify via email or whatever.
	if ( $comment_id && $is_customer_note ) {
		add_comment_meta( $comment_id, 'is_customer_note', true );

		/**
		 * Fire action new customer_note.
		 *
		 * @param \AweBooking\Model\Booking $booking The booking object.
		 * @param string                    $note    The note content.
		 */
		do_action( 'abrs_new_customer_note', $booking, $note );
	}

	return $comment_id;
}

/**
 * Delete a booking note by ID.
 *
 * @param  int $note_id The note ID.
 * @return bool
 */
function abrs_delete_booking_note( $note_id ) {
	return wp_delete_comment( $note_id, true );
}

/**
 * Exclude booking comments from queries and RSS.
 *
 * This code should exclude 'booking_note' comments from queries.
 * Some queries (like the recent comments widget on the dashboard) are hardcoded.
 *
 * @param  array $clauses The query clauses.
 * @return array
 */
function _abrs_exclude_booking_comments( $clauses ) {
	$clauses['where'] .= ( $clauses['where'] ? ' AND ' : '' ) . " comment_type != 'booking_note' ";

	return $clauses;
}
add_filter( 'comments_clauses', '_abrs_exclude_booking_comments', 10, 1 );

/**
 * Query booking data for a term and return IDs.
 *
 * Use for 'post__in' in WP_Query.
 *
 * @param  string $term The term to search.
 * @return array
 */
function abrs_search_booking_by_term( $term ) {
	global $wpdb;

	// Filters the search fields.
	$search_fields = array_map( 'abrs_clean', apply_filters( 'abrs_search_booking_fields', [
		'_customer_first_name',
		'_customer_last_name',
		'_customer_address',
		'_customer_company',
		'_customer_email',
		'_customer_phone',
	]));

	// Prepare search bookings.
	$booking_ids = [];

	if ( is_numeric( $term ) ) {
		$booking_ids[] = absint( $term );
	}

	if ( ! empty( $search_fields ) ) {
		$search1 = $wpdb->get_col($wpdb->prepare(
			"SELECT DISTINCT `p1`.`post_id` FROM {$wpdb->postmeta} AS `p1` WHERE `p1`.`meta_value` LIKE %s AND `p1`.`meta_key` IN ('" . implode( "','", array_map( 'esc_sql', $search_fields ) ) . "')", // @codingStandardsIgnoreLine
			'%' . $wpdb->esc_like( abrs_clean( $term ) ) . '%'
		));

		$search2 = $wpdb->get_col( $wpdb->prepare(
			"SELECT `booking_id` FROM `{$wpdb->prefix}awebooking_booking_items` WHERE `booking_item_name` LIKE %s",
			'%' . $wpdb->esc_like( abrs_clean( $term ) ) . '%'
		));

		$booking_ids = array_unique( array_merge( $booking_ids, $search1, $search2 ) );
	}

	return apply_filters( 'abrs_search_booking_results', $booking_ids, $term, $search_fields );
}

/**
 * Get all booking IDs booked by given a room ID.
 *
 * @param  int   $room_id  The Room ID.
 * @param  array $statuses Optional, filter by booking statuses.
 * @return array
 */
function abrs_get_booking_booked_by_room( $room_id, $statuses = [] ) {
	global $wpdb;

	$where_clause = ! empty( $statuses )
		? "AND `booking`.`post_status` IN ('" . implode( "', '", array_map( 'esc_sql', $statuses ) ) . "')"
		: '';

	$results = $wpdb->get_results( $wpdb->prepare( // @codingStandardsIgnoreStart
		"SELECT `booking`.`ID` FROM `{$wpdb->posts}` AS `booking`
		INNER JOIN `{$wpdb->prefix}awebooking_booking_items` AS `item` ON (`booking`.`ID` = `item`.`booking_id` AND `booking`.`post_type` = 'awebooking')
		INNER JOIN `{$wpdb->prefix}awebooking_booking_itemmeta` AS itemmeta ON (`item`.`booking_item_id` = `itemmeta`.`booking_item_id` AND `itemmeta`.`meta_key` = '_room_id' )
		WHERE CAST(`itemmeta`.`meta_value` AS SIGNED) = %d {$where_clause}",
		$room_id
	)); // @codingStandardsIgnoreEnd

	return $results
		? array_map( 'absint', array_column( $results, 'ID' ) )
		: [];
}
