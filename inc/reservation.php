<?php

use AweBooking\Reservation\Request;
use AweBooking\Model\Common\Guest_Counts;

/**
 * Create a reservation request.
 *
 * @param  array $args The query args.
 * @return \AweBooking\Reservation\Request|WP_Error
 */
function abrs_reservation_request( array $args ) {
	$args = wp_parse_args( $args, [
		'strict'     => is_admin() ? false : true,
		'check_in'   => '',
		'check_out'  => '',
		'adults'     => 0,
		'children'   => 0,
		'infants'    => 0,
		'options'    => [],
	]);

	// Create the timespan.
	$timespan = abrs_timespan( $args['check_in'], $args['check_out'], 1, $args['strict'] );
	if ( is_wp_error( $timespan ) ) {
		return $timespan;
	}

	// Create the guest counts.
	$guest_counts = null;
	if ( $args['adults'] > 0 ) {
		$guest_counts = new Guest_Counts( $args['adults'] );

		if ( abrs_children_bookable() && $args['children'] > 0 ) {
			$guest_counts->set_children( $args['children'] );
		}

		if ( abrs_infants_bookable() && $args['infants'] > 0 ) {
			$guest_counts->set_infants( $args['infants'] );
		}
	}

	return new Request( $timespan, $guest_counts, $args['options'] );
}
