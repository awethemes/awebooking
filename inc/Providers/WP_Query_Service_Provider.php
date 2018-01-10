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

		$total_guest = $this->get_requested_guest( $qv );
		if ( $total_guest > 0 ) {
			$pieces['join']  .= " INNER JOIN {$wpdb->postmeta} AS max_occupancy ON ({$wpdb->posts}.ID = max_occupancy.post_id AND max_occupancy.meta_key = '_maximum_occupancy') ";
			$pieces['where'] .= " AND CAST(max_occupancy.meta_value AS SIGNED) >= '" . absint( $total_guest ) . "' ";
		}

		// TODO: Remove this in next version.
		if ( ! empty( $qv['booking_nights'] ) && $qv['booking_nights'] > 0 ) {
			$pieces['join']  .= " INNER JOIN {$wpdb->postmeta} AS min_night ON ({$wpdb->posts}.ID = min_night.post_id AND min_night.meta_key = 'minimum_night') ";
			$pieces['where'] .= " AND CAST(min_night.meta_value AS SIGNED) <= '" . absint( $qv['booking_nights'] ) . "' ";
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
	 * Get requested guest from query-vars.
	 *
	 * @param  array $qv Query vars.
	 * @return int
	 */
	protected function get_requested_guest( array $qv ) {
		$total = 0;

		if ( ! empty( $qv['booking_adults'] ) && $qv['booking_adults'] > 0 ) {
			$total += (int) $qv['booking_adults'];
		}

		if ( awebooking( 'setting' )->is_children_bookable() && ! empty( $qv['booking_children'] ) && $qv['booking_children'] > 0 ) {
			$total += (int) $qv['booking_children'];
		}

		if ( awebooking( 'setting' )->is_infants_bookable() && ! empty( $qv['booking_infants'] ) && $qv['booking_infants'] > 0 ) {
			$total += (int) $qv['booking_infants'];
		}

		return $total;
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
