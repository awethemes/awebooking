<?php

namespace AweBooking\Core\Providers;

use AweBooking\Constants;
use AweBooking\Support\Service_Provider;

class Query_Service_Provider extends Service_Provider {
	/**
	 * Init service provider.
	 *
	 * @return void
	 */
	public function init() {
		// Apply query clauses in the WP_Query.
		add_filter( 'posts_clauses', [ $this, 'apply_query_clauses' ], 10, 2 );

		// Setup the awebooking objects into the main query.
		add_action( 'the_post', [ $this, 'setup_awebooking_objects' ] );
	}

	/**
	 * When `the_post()` is called, setup the awebooking objects.
	 *
	 * @param  \WP_Post $post The WP_Post object (passed by reference).
	 * @return void
	 */
	public function setup_awebooking_objects( $post ) {
		if ( empty( $post->post_type ) ) {
			return;
		}

		if ( Constants::ROOM_TYPE === $post->post_type ) {
			unset( $GLOBALS['room_type'] );
			$GLOBALS['room_type'] = abrs_get_room_type( $post );
		}

		do_action( 'abrs_setup_global_objects', $post );
	}

	/**
	 * Apply query clauses in the WP_Query.
	 *
	 * This method hook into the WP Main Query and apply custom
	 * query only in room-type post type. Please do not touch if
	 * you don't understand the awebooking system.
	 *
	 * @param  array     $pieces   The pieces clause of the query.
	 * @param  \WP_Query $wp_query The WP_Query instance.
	 * @return array
	 */
	public function apply_query_clauses( $pieces, $wp_query ) {
		global $wpdb;

		$qv = $wp_query->query_vars;

		// Working only in room-type.
		if ( Constants::ROOM_TYPE !== $qv['post_type'] ) {
			return $pieces;
		}

		// Get the total guest counts from $qv.
		$guest_counts = $this->get_guest_counts( $qv );

		if ( $guest_counts > 0 ) {
			$pieces['join']  .= " INNER JOIN {$wpdb->postmeta} AS max_occupancy ON ({$wpdb->posts}.ID = max_occupancy.post_id AND max_occupancy.meta_key = '_maximum_occupancy') ";
			$pieces['where'] .= " AND CAST(max_occupancy.meta_value AS SIGNED) >= '" . esc_sql( $guest_counts ) . "' ";
		}

		return $pieces;
	}

	/**
	 * Get guest counts from query-vars.
	 *
	 * @param  array $qv The query vars.
	 * @return int
	 */
	protected function get_guest_counts( array $qv ) {
		$total = 0;

		if ( ! empty( $qv['booking_adults'] ) && $qv['booking_adults'] > 0 ) {
			$total += (int) $qv['booking_adults'];
		}

		if ( abrs_children_bookable() && ! empty( $qv['booking_children'] ) && $qv['booking_children'] > 0 ) {
			$total += (int) $qv['booking_children'];
		}

		if ( abrs_infants_bookable() && ! empty( $qv['booking_infants'] ) && $qv['booking_infants'] > 0 ) {
			$total += (int) $qv['booking_infants'];
		}

		return $total;
	}
}
