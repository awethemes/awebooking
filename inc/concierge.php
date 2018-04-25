<?php

use AweBooking\Constants;
use AweBooking\Model\Common\Timespan;
use AweBooking\Calendar\Finder\State_Finder;
use AweBooking\Calendar\Event\Core\Pricing_Event;

/**
 * Determines if given room is available or not in a timespan.
 *
 * @param  int|array $room     The room ID to check.
 * @param  Timespan  $timespan The timespan instance of array of timespan.
 * @return bool
 */
function abrs_is_room_available( $room, Timespan $timespan ) {
	return abrs_check_room_state( $room, $timespan, Constants::STATE_AVAILABLE );
}

/**
 * Set a room as blocked in a timespan.
 *
 * @param int      $room     The room ID to apply.
 * @param Timespan $timespan The timespan.
 * @param array    $options {
 *     Optional. Options to apply the state.
 *
 *     @type array  $only_days   Apply only in special days of week.
 *     @type string $granularity Granularity by nightly or daily.
 * }
 * @return bool|null|WP_Error
 */
function abrs_block_room( $room, Timespan $timespan, $options = [] ) {
	return abrs_apply_room_state( $room, $timespan, Constants::STATE_UNAVAILABLE, $options );
}

/**
 * Unblock a room in a timespan.
 *
 * @param int      $room     The room ID to apply.
 * @param Timespan $timespan The timespan.
 * @param array    $options {
 *     Optional. Options to apply the state.
 *
 *     @type array  $only_days   Apply only in special days of week.
 *     @type string $granularity Granularity by nightly or daily.
 * }
 * @return bool|null|WP_Error
 */
function abrs_unblock_room( $room, Timespan $timespan, $options = [] ) {
	return abrs_apply_room_state( $room, $timespan, Constants::STATE_AVAILABLE, $options );
}

/**
 * Determines if given room is passed states in a timespan.
 *
 * @param  array|int $room     The room ID to check.
 * @param  Timespan  $timespan The timespan instance.
 * @param  array|int $states   A string or an array of states.
 * @return bool|WP_Error
 */
function abrs_check_room_state( $room, Timespan $timespan, $states = Constants::STATE_AVAILABLE ) {
	try {
		$timespan->requires_minimum_nights( 1 );
	} catch ( LogicException $e ) {
		return new WP_Error( 'timespan_error', $e->getMessage() );
	}

	// Correct the states input.
	$states = is_array( $states ) ? $states : [ $states ];

	/**
	 * No constraints by default, but apply filters for another.
	 *
	 * @param array                             $constraints An array of constraints.
	 * @param mixed                             $room        The given room(s) to check.
	 * @param \AweBooking\Model\Common\Timespan $timespan    The timespan instance.
	 * @param array                             $states      The states.
	 * @var array
	 */
	$constraints = apply_filters( 'awebooking/check_room_state_constraints', [], $room, $timespan, $states );

	// Transform rooms to resources.
	$resources = abrs_collect( $room )
		->map_into( AweBooking\Model\Room::class )
		->transform( 'abrs_filter_resource' );

	$response = ( new State_Finder( $resources, abrs_calendar_provider( 'state', $resources ) ) )
		->only( $states )
		->using( $constraints )
		->find( $timespan->to_period( Constants::GL_NIGHTLY ) );

	/**
	 * Apply filters after complete the check.
	 *
	 * @param \AweBooking\Calendar\Finder\Response $response The finder response.
	 * @param mixed                                $room     The given room(s) to check.
	 * @param \AweBooking\Model\Common\Timespan    $timespan The timespan instance.
	 * @param array                                $states   The states.
	 * @var array
	 */
	$response = apply_filters( 'awebooking/check_room_state_response', $response, $room, $timespan, $states );

	return count( $response->get_included() ) > 0;
}

/**
 * Perform apply a state of given room in a timespan.
 *
 * @param int      $room     The room ID to apply.
 * @param Timespan $timespan The timespan.
 * @param int      $state    The state to apply.
 * @param array    $options {
 *     Optional. Options to apply the state.
 *
 *     @type array  $only_days   Apply only in special days of week.
 *     @type string $granularity Granularity by nightly or daily.
 * }
 * @return bool|null|WP_Error
 */
function abrs_apply_room_state( $room, Timespan $timespan, $state, $options = [] ) {
	$options = wp_parse_args( $options, [
		'only_days'   => null,
		'granularity' => Constants::GL_NIGHTLY,
	]);

	try {
		$timespan->requires_minimum_nights( Constants::GL_NIGHTLY === $options['granularity'] ? 1 : 0 );
	} catch ( LogicException $e ) {
		return new WP_Error( 'timespan_error', $e->getMessage() );
	}

	// Leave if given invalid room ID.
	if ( ! $room = abrs_get_room( $room ) ) {
		return new WP_Error( 'invalid_room', esc_html__( 'Invalid Room ID', 'awebooking' ) );
	}

	/**
	 * Fire action before apply room state.
	 *
	 * @param \AweBooking\Model\Room            $room     The room model.
	 * @param \AweBooking\Model\Common\Timespan $timespan The timespan.
	 * @param int                               $state    The state to apply.
	 * @param array                             $options  Options to apply the state.
	 */
	do_action( 'awebooking/prepare_apply_room_state', $room, $timespan, $state, $options );

	// Create the state Calendar,
	// then fetch all events in given timespan.
	$calendar = abrs_calendar( $room, 'state' );

	$events = $calendar->get_events(
		$timespan->to_period( $options['granularity'] )
	);

	$stored = null;

	// We will perform set the state on each piece of events,
	// this make sure we can't touch to "booking" state in some case.
	foreach ( $events as $event ) {
		$event_state = (int) $event->get_value();

		// Ignore update same state.
		if ( $event_state == $state ) {
			continue;
		}

		// If we apply a "available" or "unavailable" state, but current
		// event is "booking" state, ignore the perform update.
		if ( in_array( $state, [ Constants::STATE_AVAILABLE, Constants::STATE_UNAVAILABLE ] ) && Constants::STATE_BOOKING == $event_state ) {
			continue;
		}

		// Apply new state.
		$event->set_state( $state );

		// Apply changes only special days.
		if ( ! empty( $options['only_days'] ) ) {
			$event->only_days( $options['only_days'] );
		}

		// Store the event in the Calendar.
		$stored = $calendar->store( $event );
	}

	/**
	 * Fire action after apply room state.
	 *
	 * @param bool                              $stored   Stored success or not.
	 * @param \AweBooking\Model\Room            $room     The room model.
	 * @param \AweBooking\Model\Common\Timespan $timespan The timespan.
	 * @param int                               $state    The state to apply.
	 * @param array                             $options  Options to apply the state.
	 */
	do_action( 'awebooking/after_apply_room_state', $stored, $room, $timespan, $state, $options );

	return $stored;
}

/**
 * Retrieve price of a rate in a timespan.
 *
 * @param  int      $rate     The rate ID to retrieve.
 * @param  Timespan $timespan The timespan.
 * @return array|WP_Error
 */
function abrs_retrieve_price( $rate, Timespan $timespan ) {
	try {
		$timespan->requires_minimum_nights( 1 );
	} catch ( LogicException $e ) {
		return new WP_Error( 'timespan_error', $e->getMessage() );
	}

	// Leave if given invalid rate ID.
	if ( $rate instanceof Rate && ! $rate = abrs_get_rate( $rate ) ) {
		return new WP_Error( 'invalid_rate', esc_html__( 'Invalid Rate ID', 'awebooking' ) );
	}

	// Get all events as itemized.
	$itemized = abrs_calendar( $rate, 'pricing' )
		->get_events( $timespan->to_period( Constants::GL_NIGHTLY ) )
		->itemize();

	// Calculate the price from itemized.
	$price = apply_filters( 'awebooking/valuation',
		abrs_decimal_raw( $itemized->sum() ), $itemized, $rate, $timespan
	);

	return [ $price, $itemized->map( 'abrs_decimal_raw' ) ];
}

/**
 * Apply custom price for a rate in a timespan.
 *
 * @param int      $rate      The rate ID to retrieve.
 * @param Timespan $timespan  The timespan.
 * @param mixed    $amount    The amount to apply.
 * @param mixed    $operation The operation apply amount @see abrs_get_rate_operations().
 * @param array    $options {
 *     Optional. Options to apply the state.
 *
 *     @type array  $only_days   Apply only in special days of week.
 *     @type string $granularity Granularity by nightly or daily.
 * }
 * @return bool|WP_Error
 */
function abrs_apply_price( $rate, Timespan $timespan, $amount, $operation = 'replace', $options = [] ) {
	$options = wp_parse_args( $options, [
		'only_days'   => null,
		'granularity' => Constants::GL_NIGHTLY,
	]);

	try {
		$timespan->requires_minimum_nights( Constants::GL_NIGHTLY === $options['granularity'] ? 1 : 0 );
	} catch ( LogicException $e ) {
		return new WP_Error( 'timespan_error', $e->getMessage() );
	}

	// Leave if given invalid rate ID.
	if ( ! $rate = abrs_get_rate( $rate ) ) {
		return new WP_Error( 'invalid_rate', esc_html__( 'Invalid Rate ID', 'awebooking' ) );
	}

	/**
	 * Fire action before apply room price.
	 *
	 * @param \AweBooking\Model\Room            $rate      The rate model.
	 * @param \AweBooking\Model\Common\Timespan $timespan  The timespan.
	 * @param int                               $amount    The amount to apply.
	 * @param mixed                             $operation The operation apply amount
	 * @param array                             $options   Options to apply the state.
	 */
	do_action( 'awebooking/prepare_apply_price', $rate, $timespan, $amount, $operation, $options );

	// Create the pricing Calendar
	// then fetch all events in given timespan.
	$calendar = abrs_calendar( $rate, 'pricing' );

	$events = $calendar->get_events(
		$timespan->to_period( $options['granularity'] )
	);

	$stored = false;

	// We will perform set the price on each piece of events.
	foreach ( $events as $event ) {
		// Apply filters for another custom the evaluate room price.
		$evaluated = apply_filters( 'awebooking/evaluate_room_price', null, $event->get_amount(), $amount, $operation, $rate, $timespan, $options );

		if ( ! is_null( $evaluated ) ) {
			$event->set_value( $evaluated );
		} else {
			$event->apply_operation( $amount, $operation );
		}

		// Apply changes only special days.
		if ( ! empty( $options['only_days'] ) ) {
			$event->only_days( $options['only_days'] );
		}

		// Store the price in the Calendar.
		$stored = $calendar->store( $event );
	}

	/**
	 * Fire action after apply room price.
	 *
	 * @param \AweBooking\Model\Room            $rate      The rate model.
	 * @param \AweBooking\Model\Common\Timespan $timespan  The timespan.
	 * @param int                               $amount    The amount to apply.
	 * @param mixed                             $operation The operation apply amount
	 * @param array                             $options   Options to apply the state.
	 */
	do_action( 'awebooking/after_apply_price', $stored, $rate, $timespan, $amount, $operation, $options );

	return $stored;
}

/**
 * Returns the rate operations.
 *
 * @return array
 */
function abrs_get_rate_operations() {
	return apply_filters( 'awebooking/rate_operations', [
		'replace'  => esc_html__( 'Replace', 'awebooking' ),
		'add'      => esc_html__( 'Add', 'awebooking' ),
		'subtract' => esc_html__( 'Subtract', 'awebooking' ),
		'multiply' => esc_html__( 'Multiply', 'awebooking' ),
		'divide'   => esc_html__( 'Divide', 'awebooking' ),
		'increase' => esc_html__( 'Increase', 'awebooking' ),
		'decrease' => esc_html__( 'Decrease', 'awebooking' ),
	]);
}
