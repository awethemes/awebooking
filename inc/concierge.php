<?php

use AweBooking\Constants;
use AweBooking\Model\Model;
use AweBooking\Model\Common\Timespan;
use AweBooking\Model\Common\Guest_Counts;
use AweBooking\Model\Pricing\Base_Rate;
use AweBooking\Reservation\Request;
use AweBooking\Calendar\Calendar;
use AweBooking\Calendar\Resource\Resource;
use AweBooking\Reservation\Pricing\Pricing;
use AweBooking\Support\Carbonate;

/**
 * Create the timespan.
 *
 * @param  array $args The timespan args.
 * @return \AweBooking\Model\Common\Timespan|WP_Error
 */
function abrs_timespan( array $args ) {
	// Parse the default args.
	$args = wp_parse_args( $args, [
		'nights'         => 0,
		'start_date'     => isset( $args['check_in'] ) ? $args['check_in'] : '',
		'end_date'       => isset( $args['check_out'] ) ? $args['check_out'] : '',
		'minimum_nights' => 0,
		'strict'         => false,
	]);

	try {
		if ( $args['nights'] > 0 ) {
			$timespan = Timespan::from( $args['start_date'], $args['nights'] );
		} else {
			$timespan = new Timespan( $args['start_date'], $args['end_date'] );
		}

		// Requires minimum 1 nights to works.
		if ( $args['minimum_nights'] > 0 ) {
			$timespan->minimum_nights( $args['minimum_nights'] );
		}

		// Validate strict mode.
		if ( $args['strict'] && $this->get_start_date()->lt( Carbonate::today() ) ) {
			return new WP_Error( esc_html__( 'The start date must the greater than or equal today.', 'awebooking' ) );
		}

		return $timespan;
	} catch ( Exception $e ) {
		return new WP_Error( 'timespan_error', esc_html__( 'The start date and end date is invalid.', 'awebooking' ) );
	}
}

/**
 * Set a room as blocked (unavailable for made reservation).
 *
 * @param  array $args The query args.
 * @return bool
 */
function abrs_block_room( array $args ) {
	$args = wp_parse_args( $args, [
		'room'       => '',
		'start_date' => '',
		'nights'     => 0,
		'end_date'   => '',
		'only_days'  => 'all',
	]);

	// Check the room instance.
	if ( empty( $args['room'] ) || ! $room = abrs_get_room( $args['room'] ) ) {
		return new WP_Error( 'invalid_room', esc_html__( 'Invalid Room ID', 'awebooking' ) );
	}

	// Create the timespan.
	$timespan = abrs_timespan([
		'start_date'     => $args['start_date'],
		'nights'         => $args['nights'],
		'end_date'       => $args['end_date'],
		'strict'         => false,
		'minimum_nights' => 1,
	]);

	// Leave if timespan error.
	if ( is_wp_error( $timespan ) ) {
		return $timespan;
	}

	// Because the Calendar work by daily, but in reservation we work
	// by nightly, so we need subtract one minute from end date.
	// This will make the Calendar query events by night instead by day.
	$timespan->set_end_date( $timespan->get_end_date()->subMinute() );

	// Get all events.
	$calendar = abrs_create_calendar( $room, 'state' );
	$events = $calendar->get_events( $timespan->to_period() );

	// Loop all events then apply state.
	foreach ( $events as $event ) {
		// Prevent update on non-available state.
		if ( Constants::STATE_AVAILABLE !== $event->get_state() ) {
			continue;
		}

		// Set the "UNAVAILABLE" state.
		$event->set_state( Constants::STATE_UNAVAILABLE );
		$event->only_days( $args['only_days'] );

		// Store the event in the Calendar.
		$calendar->store( $event );
	}

	return true;
}

/**
 * Perform custom a room price.
 *
 * @param  array $args The custom price args.
 * @return bool|WP_Error
 */
function abrs_set_room_price( array $args ) {
	// Parse the args.
	$args = wp_parse_args( $args, [
		'rate'         => '',
		'room_type'    => '',
		'nights'       => 0,
		'start_date'   => '',
		'end_date'     => '',
		'amount'       => 0,
		'operation'    => 'replace',
		'only_days'    => 'all',
	]);

	// Create the timespan.
	$timespan = abrs_timespan([
		'start_date'  => $args['start_date'],
		'end_date'    => $args['end_date'],
		'nights'      => $args['nights'],
		'strict'      => false,
	]);

	// Leave if timespan error.
	if ( is_wp_error( $timespan ) ) {
		return $timespan;
	}

	// Create the rate.
	if ( $args['rate'] === $args['room_type'] ) {
		$rate = new Base_Rate( $args['rate'] );
	}

	try {
		return ( new Pricing )->set( $rate, $timespan, $args['amount'], $args['operation'], $args['only_days'] );
	} catch ( Exception $e ) {
		return false;
	}
}

/**
 * Create a reservation request.
 *
 * @param  array $args The query args.
 * @return \AweBooking\Reservation\Request|WP_Error
 */
function abrs_create_reservation_request( array $args ) {
	$args = wp_parse_args( $args, [
		'check_in'   => '',
		'check_out'  => '',
		'nights'     => 0,

		'adults'     => 0,
		'children'   => 0,
		'infants'    => 0,

		'constraints' => [],
		'options'     => [],
	]);

	// Create the timespan.
	$timespan = abrs_timespan([
		'start_date'     => $args['check_in'],
		'end_date'       => $args['check_out'],
		'nights'         => $args['nights'],
		'strict'         => is_admin() ? false : true,
		'minimum_nights' => 1,
	]);

	if ( is_wp_error( $timespan ) ) {
		return $timespan;
	}

	// Create the guest counts.
	$guest_counts = null;
	if ( $adults > 0 ) {
		$guest_counts = new Guest_Counts( $args['adults'] );

		if ( abrs_is_children_bookable() && $args['children'] > 0 ) {
			$guest_counts->set_children( $args['children'] );
		}

		if ( abrs_is_infants_bookable() && $args['infants'] > 0 ) {
			$guest_counts->set_infants( $args['infants'] );
		}
	}

	return new Request( $timespan, $guest_counts, $args['options'] );
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
