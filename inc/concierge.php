<?php

use AweBooking\Constants;
use AweBooking\Model\Model;
use AweBooking\Model\Common\Timespan;
use AweBooking\Model\Common\Guest_Counts;
use AweBooking\Model\Pricing\Base_Rate;

use AweBooking\Calendar\Calendar;
use AweBooking\Calendar\Resource\Resource;
use AweBooking\Calendar\Event\Core\Pricing_Event;

use AweBooking\Reservation\Request;
use AweBooking\Support\Carbonate;

/**
 * Set a room as blocked.
 *
 * @see abrs_apply_state()
 *
 * @param  array $args The arguments.
 * @return bool|WP_Error
 */
function abrs_block_room( $args ) {
	return abrs_apply_state( Constants::STATE_UNAVAILABLE, $args );
}

/**
 * Unblock a room.
 *
 * @see abrs_apply_state()
 *
 * @param  array $args The arguments.
 * @return bool|WP_Error
 */
function abrs_unblock_room( $args ) {
	return abrs_apply_state( Constants::STATE_AVAILABLE, $args );
}

/**
 * Set a room state for a timespan (unavailable or available).
 *
 * @param  int   $state The state (only 1 or 0).
 * @param  array $args  The arguments.
 * @return bool|WP_Error
 */
function abrs_apply_state( $state, $args ) {
	$args = wp_parse_args( $args, [
		'room'        => 0,
		'start_date'  => '',
		'end_date'    => '',
		'only_days'   => 'all',
		'granularity' => Constants::GL_NIGHTLY,
	]);

	$prepared = _abrs_prepare_room_state( $args );

	if ( is_wp_error( $prepared ) ) {
		return $prepared;
	}

	list( $calendar, $events ) = $prepared;

	// Fire action before apply room state.
	do_action( 'awebooking/prepare_apply_room_state', $state, $args );

	foreach ( $events as $event ) {
		if ( $event->get_value() == $state ) {
			continue;
		}

		$event->set_state( $state );

		// Set event only days.
		if ( is_array( $args['only_days'] ) && ! empty( $args['only_days'] ) ) {
			$event->only_days( $args['only_days'] );
		}

		// Store the event in the Calendar.
		$calendar->store( $event );
	}

	/**
	 * Fire action after set room state.
	 *
	 * @param int   $state    The state.
	 * @param array $args     The arguments.
	 */
	do_action( 'awebooking/after_apply_room_state', $state, $args );

	return true;
}

function abrs_retrieve_state( $args ) {
}

/**
 * [_abrs_prepare_room_state description]
 *
 * @param  [type] $args [description]
 * @return [type]
 */
function _abrs_prepare_room_state( $args ) {
	$timespan = abrs_create_timespan([
		'strict'     => false,
		'start_date' => $args['start_date'],
		'end_date'   => $args['end_date'],
		'min_nights' => ( Constants::GL_NIGHTLY === $args['granularity'] ) ? 1 : 0,
	]);

	// Leave if timespan error.
	if ( is_wp_error( $timespan ) ) {
		return $timespan;
	}

	// Check the room exists.
	if ( empty( $args['room'] ) || ! $room = abrs_get_room( $args['room'] ) ) {
		return new WP_Error( 'invalid_room', esc_html__( 'Invalid Room ID', 'awebooking' ) );
	}

	// Create the calendar and get all events.
	$calendar = abrs_create_calendar( $room, 'state' );
	$events   = $calendar->get_events( $timespan->to_period( $args['granularity'] ) );

	return [ $calendar, $events ];
}

/**
 * Apply a custom price for a given room type.
 *
 * @param  array $args The custom price args.
 * @return bool|WP_Error
 */
function abrs_apply_price( array $args ) {
	$args = wp_parse_args( $args, [
		'room_type'    => '',
		'rate'         => '',
		'start_date'   => '',
		'end_date'     => '',
		'amount'       => 0,
		'operation'    => 'replace',
		'only_days'    => 'all',
		'granularity'  => Constants::GL_DAILY,
	]);

	// Create the timespan.
	$timespan = abrs_create_timespan([
		'start_date'  => $args['start_date'],
		'end_date'    => $args['end_date'],
		'min_nights'  => ( Constants::GL_NIGHTLY === $args['granularity'] ) ? 1 : 0,
		'strict'      => false,
	]);

	// Leave if timespan error.
	if ( is_wp_error( $timespan ) ) {
		return $timespan;
	}

	// Check the room type exists.
	if ( empty( $args['room_type'] ) || ! $room_type = abrs_get_room_type( $args['room_type'] ) ) {
		return new WP_Error( 'invalid_room_type', esc_html__( 'Invalid Room Type ID', 'awebooking' ) );
	}

	// Resolve the room rate.
	if ( empty( $args['rate'] ) || $args['rate'] == $room_type->get_id() ) {
		$rate = new Base_Rate( $room_type->get_id() );
	}

	// Fire action before apply room state.
	do_action( 'awebooking/prepare_apply_room_price', $args );

	// Create the calendar and get all events.
	$resource = new Resource( $rate->get_id(), $rate->get_rack_rate()->as_raw_value() );

	$calendar = abrs_create_calendar( $resource, 'pricing' );

	$period = $timespan->to_period( $args['granularity'] );

	// In case we replace the price, just create an event in the timespan.
	// Otherwise, get all events stored. This will avoid perform to many queries.
	if ( 'replace' === $args['operation'] ) {
		$events = [ new Pricing_Event( $resource, $period->get_start_date(), $period->get_end_date() ) ];
	} else {
		$events = $calendar->get_events( $period );
	}

	// Apply each piece of events.
	foreach ( $events as $event ) {
		$event->apply_operation( $args['amount'], $args['operation'] );

		$event->only_days( $args['only_days'] );

		$calendar->store( $event );
	}

	// Fire action after set room state.
	do_action( 'awebooking/after_apply_room_price', $args );

	return true;
}

/**
 * Get the price of rate in a timespan.
 *
 * @param  array $args The custom price args.
 * @return array( $total, $breakdown )
 */
function abrs_retrieve_price( array $args ) {
	$args = wp_parse_args( $args, [
		'rate'         => '',
		'room_type'    => '',
		'start_date'   => '',
		'end_date'     => '',
		'granularity'  => Constants::GL_NIGHTLY,
	]);

	// Create the timespan.
	$timespan = abrs_create_timespan([
		'start_date'  => $args['start_date'],
		'end_date'    => $args['end_date'],
		'min_nights'  => ( Constants::GL_NIGHTLY === $args['granularity'] ) ? 1 : 0,
		'strict'      => false,
	]);

	// Leave if timespan error.
	if ( is_wp_error( $timespan ) ) {
		return $timespan;
	}

	// Check the room type exists.
	if ( empty( $args['room_type'] ) || ! $room_type = abrs_get_room_type( $args['room_type'] ) ) {
		return new WP_Error( 'invalid_room_type', esc_html__( 'Invalid Room Type ID', 'awebooking' ) );
	}

	// Resolve the room rate.
	if ( empty( $args['rate'] ) || $args['rate'] == $room_type->get_id() ) {
		$rate = new Base_Rate( $room_type->get_id() );
	}

	// Create the calendar and get all events.
	$resource = new Resource( $rate->get_id(), $rate->get_rack_rate()->as_raw_value() );

	// Get the events itemized.
	$itemized = abrs_create_calendar( $resource, 'pricing' )
		->get_events( $timespan->to_period( $args['granularity'] ) )
		->itemize();

	// Calcuate price & breakdown.
	$total = abrs_decimal_raw( $itemized->sum() );

	$breakdown = $itemized->map( function( $amount ) {
		return abrs_decimal_raw( $amount );
	});

	return [ $total, $breakdown ];
}

/**
 * Create the timespan.
 *
 * @param  array $args The timespan args.
 * @return \AweBooking\Model\Common\Timespan|WP_Error
 */
function abrs_create_timespan( array $args ) {
	// Parse the default args.
	$args = wp_parse_args( $args, [
		'nights'     => 0,
		'start_date' => isset( $args['check_in'] ) ? $args['check_in'] : '',
		'end_date'   => isset( $args['check_out'] ) ? $args['check_out'] : '',
		'min_nights' => 1,
		'strict'     => false,
	]);

	try {
		if ( $args['nights'] > 0 ) {
			$timespan = Timespan::from( $args['start_date'], $args['nights'] );
		} else {
			$timespan = new Timespan( $args['start_date'], $args['end_date'] );
		}

		// Requires minimum 1 nights to works.
		if ( $args['min_nights'] > 0 ) {
			$timespan->requires_minimum_nights( $args['min_nights'] );
		}

		// Validate strict mode.
		/*if ( $args['strict'] && $timespan->get_start_date()->lt( Carbonate::today() ) ) {
			return new WP_Error( esc_html__( 'The start date must the greater than or equal today.', 'awebooking' ) );
		}*/

		return apply_filters( 'awebooking/timespan', $timespan, $args );
	} catch ( Exception $e ) {
		return new WP_Error( 'timespan_error', esc_html__( 'The start date and end date is invalid.', 'awebooking' ) );
	}
}

/**
 * Create the calendar.
 *
 * @param  int    $resource The resource ID.
 * @param  string $provider The provider name.
 * @return \AweBooking\Calendar\Calendar
 *
 * @throws InvalidArgumentException
 */
function abrs_create_calendar( $resource, $provider = 'state' ) {
	// Providers classmap.
	$providers = apply_filters( 'awebooking/calendar_providers_classmap', [
		'state'   => AweBooking\Calendar\Provider\Core\State_Provider::class,
		'booking' => AweBooking\Calendar\Provider\Core\Booking_Provider::class,
		'pricing' => AweBooking\Calendar\Provider\Core\Pricing_Provider::class,
	]);

	if ( ! array_key_exists( $provider, $providers ) ) {
		throw new InvalidArgumentException( esc_html__( 'Invalid calendar provider', 'awebooking' ) );
	}

	// Create the resource.
	if ( ! $resource instanceof Resource ) {
		$resource = new Resource( ( $resource instanceof Model ) ? $resource->get_id() : $resource );
	}

	// Apply filter allow user can change this provider.
	$provider = apply_filters( 'awebooking/calendar_provider_class', $providers[ $provider ], $provider, $resource );

	return new Calendar( $resource, new $provider( $resource ) );
}

/**
 * Create a reservation request.
 *
 * @param  array $args The query args.
 * @return \AweBooking\Reservation\Request|WP_Error
 */
function abrs_reservation_request( array $args ) {
	$args = wp_parse_args( $args, [
		'check_in'   => '',
		'check_out'  => '',

		'adults'     => 0,
		'children'   => 0,
		'infants'    => 0,

		'constraints' => [],
		'options'     => [],
	]);

	// Create the timespan.
	$timespan = abrs_create_timespan([
		'start_date'     => $args['check_in'],
		'end_date'       => $args['check_out'],
		'strict'         => is_admin() ? false : true,
		'min_nights' => 1,
	]);

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
