<?php
namespace AweBooking\Providers;

use AweBooking\Factory;
use AweBooking\Constants;
use AweBooking\Support\Service_Provider;

class WP_Query_Service_Provider extends Service_Provider {
	/**
	 * Init service provider.
	 */
	public function init() {
		// Apply query clauses in the WP_Query.
		add_filter( 'posts_clauses', [ $this, 'apply_query_clauses' ], 10, 2 );

		// Setup the awebooking objects into the main query.
		add_action( 'the_post', array( $this, 'setup_awebooking_objects' ) );

		// ...
		add_action( 'pre_get_posts', [ $this, 'loction_filter' ], 10, 1 );
	}

	/**
	 * Apply query clauses in the WP_Query.
	 *
	 * This method hook into the WP Main Query and apply custom
	 * query only in room-type post type. Please do not touch if
	 * you don't understand the awebooking system.
	 *
	 * @param  string   $pieces   The pieces clause of the query.
	 * @param  WP_Query $wp_query The WP_Query instance.
	 * @return array
	 */
	public function apply_query_clauses( $pieces, $wp_query ) {
		global $wpdb;
		$qv = $wp_query->query_vars;

		// Working only in room-type.
		if ( Constants::ROOM_TYPE !== $qv['post_type'] ) {
			return $pieces;
		}

		// Booking request query.
		if ( ! empty( $qv['booking_nights'] ) && $qv['booking_nights'] > 0 ) {
			$pieces['join']  .= " INNER JOIN {$wpdb->postmeta} AS min_night ON ({$wpdb->posts}.ID = min_night.post_id AND min_night.meta_key = 'minimum_night') ";
			$pieces['where'] .= " AND CAST(min_night.meta_value AS SIGNED) <= '" . absint( $qv['booking_nights'] ) . "' ";
		}

		if ( ! empty( $qv['booking_adults'] ) && $qv['booking_adults'] > 0 ) {
			$pieces['join']  .= " INNER JOIN {$wpdb->postmeta} AS adults ON ({$wpdb->posts}.ID = adults.post_id AND adults.meta_key = 'number_adults') ";
			$pieces['join']  .= " INNER JOIN {$wpdb->postmeta} AS max_adults ON ({$wpdb->posts}.ID = max_adults.post_id AND max_adults.meta_key = 'max_adults') ";
			$pieces['where'] .= " AND (CAST(adults.meta_value AS SIGNED) + CAST(max_adults.meta_value AS SIGNED)) >= '" . absint( $qv['booking_adults'] ) . "' ";
		}

		if ( awebooking( 'setting' )->get_children_bookable() && ! empty( $qv['booking_children'] ) && $qv['booking_children'] > 0 ) {
			$pieces['join']  .= " INNER JOIN {$wpdb->postmeta} AS children ON ({$wpdb->posts}.ID = children.post_id AND children.meta_key = 'number_children') ";
			$pieces['join']  .= " INNER JOIN {$wpdb->postmeta} AS max_children ON ({$wpdb->posts}.ID = max_children.post_id AND max_children.meta_key = 'max_children') ";
			$pieces['where'] .= " AND (CAST(children.meta_value AS SIGNED) + CAST(max_children.meta_value AS SIGNED)) >= '" . absint( $qv['booking_children'] ) . "' ";
		}

		if ( awebooking( 'setting' )->get_infants_bookable() && ! empty( $qv['booking_infants'] ) && $qv['booking_infants'] > 0 ) {
			$pieces['join']  .= " INNER JOIN {$wpdb->postmeta} AS infants ON ({$wpdb->posts}.ID = infants.post_id AND infants.meta_key = 'number_infants') ";
			$pieces['join']  .= " INNER JOIN {$wpdb->postmeta} AS max_infants ON ({$wpdb->posts}.ID = max_infants.post_id AND max_infants.meta_key = 'max_infants') ";
			$pieces['where'] .= " AND (CAST(infants.meta_value AS SIGNED) + CAST(max_infants.meta_value AS SIGNED)) >= '" . absint( $qv['booking_infants'] ) . "' ";
		}

		// Custom order by "booking_rooms".
		if ( isset( $qv['orderby'] ) && 'booking_rooms' === $qv['orderby'] ) {
			$pieces['join']    .= " LEFT JOIN {$wpdb->prefix}awebooking_rooms AS booking_rooms ON ({$wpdb->posts}.ID = booking_rooms.room_type) ";
			$pieces['orderby']  = "COUNT(booking_rooms.id) {$qv['order']}";
			$pieces['groupby']  = "{$wpdb->posts}.ID";
		}

		return $pieces;
	}

	/**
	 * When `the_post()` is called, setup the awebooking objects.
	 *
	 * @param  WP_Post $post The WP_Post object (passed by reference).
	 * @return mixed
	 */
	public function setup_awebooking_objects( $post ) {
		if ( empty( $post->post_type ) ) {
			return;
		}

		switch ( $post->post_type ) {
			case Constants::ROOM_TYPE:
				unset( $GLOBALS['room_type'] );
				$GLOBALS['room_type'] = Factory::get_room_type( $post );
				break;

			case Constants::BOOKING:
				unset( $GLOBALS['the_booking'] );
				$GLOBALS['the_booking'] = Factory::get_booking( $post );
				break;
		}

		do_action( 'awebooking/setup_objects', $post );
	}

	/**
	 * Filter by locations.
	 *
	 * TODO: Improve this.
	 *
	 * @param  array $query //.
	 * @return array
	 */
	public function loction_filter( $query ) {
		$meta_query = $query->get( 'meta_query' );

		if ( is_admin() || ! $query->is_main_query() ) {
			return;
		}

		if ( ! is_post_type_archive( Constants::ROOM_TYPE ) || ! awebooking_option( 'enable_location' ) ) {
			return;
		}

		if ( ! empty( $_REQUEST['location'] ) ) {
			$term = sanitize_text_field( wp_unslash( $_REQUEST['location'] ) );
		} else {
			$term = awebooking( 'config' )->get_default_hotel_location();
			$term = isset( $term->slug ) ? $term->slug : '';
		}

		$query->set( 'tax_query', [
			[
				'terms'    => $term,
				'field'    => 'slug',
				'taxonomy' => Constants::HOTEL_LOCATION,
			],
		]);
	}
}
