<?php

use AweBooking\Constants;
use AweBooking\Model\Booking;
use AweBooking\Model\Common\Timespan;
use AweBooking\Model\Common\Guest_Counts;
use AweBooking\Model\Pricing\Contracts\Rate_Interval;
use AweBooking\Calendar\Resource\Resource;
use AweBooking\Calendar\Finder\Finder;
use AweBooking\Calendar\Finder\State_Finder;
use AweBooking\Calendar\Event\Core\Booking_Event;
use AweBooking\Availability\Request;
use AweBooking\Availability\Room_Rate;
use AweBooking\Availability\Constraints\Night_Stay_Constraint;
use WPLibs\Http\Request as Http_Request;
use AweBooking\Support\Collection;
use Illuminate\Support\Arr;
use AweBooking\Availability\Constraints\Checkin_Days_Constraint;
use AweBooking\Availability\Constraints\Period_Bookable_Constraint;

/**
 * Returns list of states represent for blocked.
 *
 * @return array
 */
function abrs_get_blocked_states() {
	return apply_filters( 'abrs_blocked_states', [
		Constants::STATE_SYNC        => [
			'name'  => 'sync',
			'label' => esc_html__( 'Sync', 'awebooking' ),
		],
		Constants::STATE_UNAVAILABLE => [
			'name'  => 'unavailable',
			'label' => esc_html__( 'Unavailable', 'awebooking' ),
		],
	] );
}

/**
 * Returns list of operations.
 *
 * @return array
 */
function abrs_get_rate_operations() {
	return apply_filters( 'abrs_rate_operations', [
		'replace'  => esc_html__( 'Replace', 'awebooking' ),
		'add'      => esc_html__( 'Add', 'awebooking' ),
		'subtract' => esc_html__( 'Subtract', 'awebooking' ),
		'multiply' => esc_html__( 'Multiply', 'awebooking' ),
		'divide'   => esc_html__( 'Divide', 'awebooking' ),
		'increase' => esc_html__( 'Increase', 'awebooking' ),
		'decrease' => esc_html__( 'Decrease', 'awebooking' ),
	] );
}

/**
 * Determines if a given room is "available" in a timespan.
 *
 * @param  Collection|array|int $room     The room to check.
 * @param  Timespan             $timespan The timespan.
 *
 * @return bool
 */
function abrs_room_available( $room, Timespan $timespan ) {
	return abrs_room_has_states( $room, $timespan, Constants::STATE_AVAILABLE );
}

/**
 * Determines if a given room has given states.
 *
 * @param  Collection|int|array $room     The room to check.
 * @param  Timespan             $timespan The timespan.
 * @param  array|string         $states   The states to check.
 *
 * @return bool
 */
function abrs_room_has_states( $room, Timespan $timespan, $states ) {
	$response = abrs_check_room_states( $room, $timespan, $states );

	if ( is_wp_error( $response ) ) {
		return false;
	}

	return count( $response->get_included() ) > 0;
}

/**
 * Check given rooms states.
 *
 * @param  Collection|array|int $room        The room ID to check.
 * @param  Timespan             $timespan    The timespan.
 * @param  array|int            $states      A string or an array of states.
 * @param  array                $constraints AweBooking\Calendar\Finder\Constraint[].
 * @param  string               $comparison  The comparison mode (any, only, without).
 *
 * @return \AweBooking\Calendar\Finder\Response|WP_Error
 */
function abrs_check_room_states( $room, Timespan $timespan, $states = Constants::STATE_AVAILABLE, array $constraints = [], $comparison = 'only' ) {
	try {
		$timespan->requires_minimum_nights( 1 );
	} catch ( LogicException $e ) {
		return new WP_Error( 'timespan_error', $e->getMessage() );
	}

	$resources = abrs_collect( $room )
		->transform( 'abrs_resource_room' )
		->filter()
		->all();

	$constraints   = apply_filters( 'abrs_room_contraints', $constraints, $room, $timespan );
	// $constraints[] = new Checkin_Days_Constraint( $resources, $timespan );
	// $constraints[] = new Period_Bookable_Constraint( $resources, $timespan );

	$response = ( new State_Finder( $resources, abrs_calendar_provider( 'state', $resources, true ) ) )
		->filter( $comparison, $states )
		->using( $constraints )
		->find( $timespan->to_period( Constants::GL_NIGHTLY ) );

	return apply_filters( 'abrs_check_room_state_response', $response, $resources, $timespan, $states, $constraints, $comparison );
}

/**
 * Set a room as blocked in a timespan.
 *
 * @param int      $room        The room ID to apply.
 * @param Timespan $timespan    The timespan.
 * @param array    $options     {
 *                              Optional. Options to apply the state.
 *
 * @type array     $only_days   Apply only in specified days of week.
 * @type string    $granularity Granularity by nightly or daily.
 * }
 * @return bool|null|WP_Error
 */
function abrs_block_room( $room, Timespan $timespan, $options = [] ) {
	return abrs_apply_room_state( $room, $timespan, Constants::STATE_UNAVAILABLE, $options );
}

/**
 * Unblock a room in a timespan.
 *
 * @param int      $room        The room ID to apply.
 * @param Timespan $timespan    The timespan.
 * @param array    $options     {
 *                              Optional. Options to apply the state.
 *
 * @type array     $only_days   Apply only in specified days of week.
 * @type string    $granularity Granularity by nightly or daily.
 * }
 * @return bool|null|WP_Error
 */
function abrs_unblock_room( $room, Timespan $timespan, $options = [] ) {
	return abrs_apply_room_state( $room, $timespan, Constants::STATE_AVAILABLE, $options );
}

/**
 * Perform apply a state of given room in a timespan.
 *
 * @param int      $room        The room ID to apply.
 * @param Timespan $timespan    The timespan.
 * @param int      $state       The state to apply.
 * @param array    $options     {
 *                              Optional. Options to apply the state.
 *
 * @type array     $only_days   Apply only in specified days of week.
 * @type string    $granularity Granularity by nightly or daily.
 * }
 * @return bool|null|WP_Error
 */
function abrs_apply_room_state( $room, Timespan $timespan, $state, $options = [] ) {
	$options = wp_parse_args( $options, [
		'only_days'   => null,
		'granularity' => Constants::GL_NIGHTLY,
	] );

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
	do_action( 'abrs_prepare_apply_room_state', $room, $timespan, $state, $options );

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
		/* @var $event \AweBooking\Calendar\Event\Event|\AweBooking\Calendar\Event\Core\State_Event */
		$event_state = (int) $event->get_value();

		// Ignore update same state.
		if ( $event_state === (int) $state ) {
			continue;
		}

		// If we apply a "available" or "unavailable" state, but current
		// event is "booking" state, ignore the perform update.
		if ( Constants::STATE_BOOKING === $event_state && in_array( $state, [ Constants::STATE_AVAILABLE, Constants::STATE_UNAVAILABLE ] ) ) {
			continue;
		}

		// Apply new state.
		$event->set_state( $state );

		// Apply changes only specified days.
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
	do_action( 'abrs_after_apply_room_state', $stored, $room, $timespan, $state, $options );

	return $stored;
}

/**
 * Retrieve breakdown of rate interval in a timespan.
 *
 * @param  int|mixed                         $rate     The rate ID to retrieve.
 * @param  \AweBooking\Model\Common\Timespan $timespan The timespan.
 *
 * @return \AweBooking\Calendar\Event\Itemized|WP_Error
 */
function abrs_retrieve_rate( $rate, Timespan $timespan ) {
	try {
		$timespan->requires_minimum_nights( 1 );
	} catch ( LogicException $e ) {
		return new WP_Error( 'timespan_error', $e->getMessage() );
	}

	// Allow 3thirty plugins custom retrieve rate method.
	if ( $_result = apply_filters( 'abrs_prepare_retrieve_rate', null, $rate, $timespan ) ) {
		return $_result;
	}

	if ( ! $rate instanceof Rate_Interval || ! $rate = abrs_get_rate_interval( $rate ) ) {
		return new WP_Error( 'invalid_rate', esc_html__( 'Invalid rate ID', 'awebooking' ) );
	}

	// Transform the rate to the calendar resource.
	$resource = abrs_filter_resource( $rate );

	// Get all events as itemized.
	$itemized = abrs_calendar( $resource, 'pricing', true )
		->get_events( $timespan->to_period( Constants::GL_NIGHTLY ) )
		->itemize();

	// Correct the amount.
	$breakdown = $itemized->transform( function ( $a ) {
		return abrs_decimal_raw( $a )->as_numeric();
	} );

	return apply_filters( 'abrs_retrieve_rate_breakdown', $breakdown, $rate, $timespan );
}

/**
 * Apply custom price for a rate in a timespan.
 *
 * @param int      $rate        The rate ID to retrieve.
 * @param Timespan $timespan    The timespan.
 * @param mixed    $amount      The amount to apply.
 * @param mixed    $operation   The operation apply amount @see abrs_get_rate_operations().
 * @param array    $options     {
 *                              Optional. Options to apply the state.
 *
 * @type array     $only_days   Apply only in specified days of week.
 * @type string    $granularity Granularity by nightly or daily.
 * }
 * @return bool|WP_Error
 */
function abrs_apply_rate( $rate, Timespan $timespan, $amount, $operation = 'replace', $options = [] ) {
	$options = wp_parse_args( $options, [
		'only_days'   => null,
		'granularity' => Constants::GL_NIGHTLY,
	] );

	try {
		$timespan->requires_minimum_nights( Constants::GL_NIGHTLY === $options['granularity'] ? 1 : 0 );
	} catch ( LogicException $e ) {
		return new WP_Error( 'timespan_error', $e->getMessage() );
	}

	// Leave if given invalid rate ID.
	if ( ! $rate instanceof Rate_Interval && ! $rate = abrs_get_rate_interval( $rate ) ) {
		return new WP_Error( 'invalid_rate', esc_html__( 'Invalid rate ID', 'awebooking' ) );
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
	do_action( 'abrs_prepare_apply_price', $rate, $timespan, $amount, $operation, $options );

	// Create the pricing Calendar
	// then fetch all events in given timespan.
	$calendar = abrs_calendar( $rate, 'pricing' );

	$events = $calendar->get_events(
		$timespan->to_period( $options['granularity'] )
	);

	$stored = false;

	// We will perform set the price on each piece of events.
	foreach ( $events as $event ) {
		/* @var $event \AweBooking\Calendar\Event\Core\Pricing_Event */

		// Apply filters for another custom the evaluate room price.
		$evaluated = apply_filters( 'abrs_evaluate_room_price', null, $event->get_amount(), $amount, $operation, $rate, $timespan, $options );

		if ( ! is_null( $evaluated ) ) {
			$event->set_value( $evaluated );
		} else {
			$event->apply_operation( $amount, $operation );
		}

		// Apply changes only specified days.
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
	do_action( 'abrs_after_apply_price', $stored, $rate, $timespan, $amount, $operation, $options );

	return $stored;
}

/**
 * Filter given rates by request.
 *
 * @param  Collection|array|int $rates       The rates.
 * @param  Timespan             $timespan    The timespan.
 * @param  array                $constraints Array of constraints.
 * @return \AweBooking\Calendar\Finder\Response
 */
function abrs_filter_rate_intervals( $rates, Timespan $timespan, $constraints = [] ) {
	$resources = abrs_collect( $rates )
		->transform( 'abrs_resource_rate' )
		->filter( /* Remove empty items */ )
		->each( function ( Resource $r ) use ( $timespan ) {
			$r->set_constraints( abrs_build_rate_constraints( $r->get_reference(), $timespan ) );
		} );

	$response = ( new Finder( $resources->all() ) )
		->callback( '_abrs_filter_rates_callback' )
		->using( apply_filters( 'abrs_filter_rate_intervals_constraints', $constraints, $timespan, $resources ) )
		->find( $timespan->to_period( Constants::GL_NIGHTLY ) );

	return apply_filters( 'abrs_filter_rate_intervals', $response, $timespan, $resources );
}

/**
 * Perform rates filter callback.
 *
 * @param  \AweBooking\Calendar\Resource\Resource $resource The resource.
 * @param  \AweBooking\Calendar\Finder\Response   $response The finder response.
 * @return void
 */
function _abrs_filter_rates_callback( $resource, $response ) {
	$effective_date = $resource->get_reference()->get_effective_date();
	$expires_date   = $resource->get_reference()->get_expires_date();

	$today = abrs_date_time( 'today' );

	if ( $effective_date && $today < abrs_date_time( $effective_date ) ) {
		$response->add_miss( $resource, 'rate_effective_date' );

		return;
	}

	if ( $expires_date && $today > abrs_date_time( $expires_date ) ) {
		$response->add_miss( $resource, 'rate_expired_date' );

		return;
	}

	$response->add_match( $resource, 'rate_valid_dates' );
}

/**
 * Build the rate constraints based on reservation request.
 *
 * @param  \AweBooking\Model\Pricing\Contracts\Rate_Interval $rate     The rate instance.
 * @param  \AweBooking\Model\Common\Timespan                 $timespan The timespan.
 * @return array
 */
function abrs_build_rate_constraints( Rate_Interval $rate, Timespan $timespan ) {
	// Get rate restrictions.
	$restrictions = $rate->get_restrictions();

	$constraints = [];
	if ( $restrictions['min_los'] || $restrictions['max_los'] ) {
		$constraints[] = new Night_Stay_Constraint( $rate->get_id(), $timespan, $restrictions['min_los'], $restrictions['max_los'] );
	}

	return apply_filters( 'abrs_rate_constraints', $constraints, $rate, $timespan );
}

/**
 * Retrieve a room rate by given an array args.
 *
 * @param  array $args The query args.
 * @return \AweBooking\Availability\Room_Rate|\WP_Error
 */
function abrs_retrieve_room_rate( $args ) {
	$args = wp_parse_args( $args, [
		'request'   => null,
		'room_type' => 0,
		'rate_plan' => 0,
		'check_in'  => '',
		'check_out' => '',
		'adults'    => 1,
		'children'  => 0,
		'infants'   => 0,
	] );

	// Find the room type.
	if ( ! $room_type = abrs_get_room_type( $args['room_type'] ) ) {
		return new WP_Error( 'invalid_room_type', esc_html__( 'Invalid room type.', 'awebooking' ) );
	}

	// Find the rate plan.
	$rate_plan = ! $args['rate_plan']
		? abrs_get_base_rate( $room_type )
		: abrs_get_rate( $args['rate_plan'] );

	if ( ! $rate_plan ) {
		return new WP_Error( 'invalid_rate_plan', esc_html__( 'Invalid rate plan.', 'awebooking' ) );
	}

	// Resolve the res request.
	$res_request = $args['request'];
	if ( ! $res_request instanceof Request ) {
		$res_request = abrs_create_res_request( Arr::except( $args, 'request' ) );
	}

	if ( ! $res_request || is_wp_error( $res_request ) ) {
		return new WP_Error( 'res_request', esc_html__( 'Unable to create the reservation request.', 'awebooking' ) );
	}

	try {
		$room_rate = new Room_Rate( $res_request, $room_type, $rate_plan );
		$room_rate->setup();
	} catch ( \Exception $e ) {
		return new WP_Error( 'room_rate', $e->getMessage() );
	}

	return $room_rate;
}

/**
 * Create new reservation request.
 *
 * @param  \WPLibs\Http\Request|array $args     The query args.
 * @param  bool                       $wp_error Optional. Whether to return a WP_Error on failure.
 * @return \AweBooking\Availability\Request|\WP_Error|null
 */
function abrs_create_res_request( $args, $wp_error = false ) {
	$http_request = null;

	if ( $args instanceof Http_Request ) {
		$http_request = $args;

		$args = $args->all();
		$args['merge_http_request'] = true;
	}

	$args = wp_parse_args( $args, [
		'strict'             => is_admin() ? false : true,
		'hotel'              => 0,
		'check_in'           => isset( $args['check-in'] ) ? $args['check-in'] : '',
		'check_out'          => isset( $args['check-out'] ) ? $args['check-out'] : '',
		'adults'             => 1,
		'children'           => 0,
		'infants'            => 0,
		'merge_http_request' => false,
	] );

	// Create the timespan.
	$timespan = abrs_timespan( $args['check_in'], $args['check_out'], 1, $args['strict'] );

	if ( is_wp_error( $timespan ) ) {
		return $wp_error ? $timespan : null;
	}

	// Create the guest counts.
	$guest_counts = new Guest_Counts( $args['adults'] );

	if ( $args['children'] > 0 && abrs_children_bookable() ) {
		$guest_counts->set_children( $args['children'] );
	}

	if ( $args['infants'] > 0 && abrs_infants_bookable() ) {
		$guest_counts->set_infants( $args['infants'] );
	}

	// Resolve http request.
	$http_request = $http_request ?: abrs_http_request();

	$res_request = new Request(
		$timespan, $guest_counts, $http_request, $args['merge_http_request']
	);

	if ( $hotel = abrs_get_hotel( $args['hotel'] ) ) {
		$res_request->set_hotel( $hotel );
	}

	return apply_filters( 'abrs_reservation_request', $res_request );
}

/**
 * Perform apply the booking state.
 *
 * @param  int      $room     The room ID.
 * @param  int      $booking  The booking ID.
 * @param  Timespan $timespan The timespan.
 * @return bool|WP_Error
 */
function abrs_apply_booking_event( $room, $booking, Timespan $timespan ) {
	try {
		$timespan->requires_minimum_nights( 1 );
	} catch ( LogicException $e ) {
		return new WP_Error( 'timespan_error', $e->getMessage() );
	}

	// Leave if given invalid room ID.
	if ( ! $room = abrs_get_room( $room ) ) {
		return new WP_Error( 'invalid_room', esc_html__( 'Invalid Room ID', 'awebooking' ) );
	}

	// Start the db transaction.
	abrs_db_transaction( 'start' );

	$booking  = ( $booking instanceof Booking ) ? $booking->get_id() : (int) $booking;
	$resource = abrs_filter_resource( $room->get_id(), 0 );

	// Store booking state & availability state.
	try {
		$period = $timespan->to_period( Constants::GL_NIGHTLY );

		$stored  = abrs_calendar( $resource, 'booking' )->store( new Booking_Event( $resource, $period->get_start_date(), $period->get_end_date(), $booking ) );
		$stored2 = abrs_apply_room_state( $room->get_id(), $timespan, Constants::STATE_BOOKING );

		if ( ! $stored || ! $stored2 ) {
			abrs_db_transaction( 'rollback' );

			return false;
		}
	} catch ( Exception $e ) {
		abrs_db_transaction( 'rollback' );

		return false;
	}

	// Everything is ok, commit the transaction.
	abrs_db_transaction( 'commit' );

	return true;
}

/**
 * Perform clear the booking state.
 *
 * @param  int      $room     The room ID.
 * @param  int      $booking  The booking ID.
 * @param  Timespan $timespan The timespan.
 * @return bool|WP_Error
 */
function abrs_clear_booking_event( $room, $booking, Timespan $timespan ) {
	try {
		$timespan->requires_minimum_nights( 1 );
	} catch ( LogicException $e ) {
		return new WP_Error( 'timespan_error', $e->getMessage() );
	}

	// Leave if given invalid room ID.
	if ( ! $room = abrs_get_room( $room ) ) {
		return new WP_Error( 'invalid_room', esc_html__( 'Invalid Room ID', 'awebooking' ) );
	}

	$period  = $timespan->to_period( Constants::GL_NIGHTLY );
	$booking = ( $booking instanceof Booking ) ? $booking->get_id() : (int) $booking;

	$statecal   = abrs_calendar( $room, 'state' );
	$bookingcal = abrs_calendar( $room, 'booking' );

	foreach ( $bookingcal->get_events( $period ) as $event ) {
		/* @var $event \AweBooking\Calendar\Event\Event */
		if ( $event->get_value() === (int) $booking ) {
			$event->set_value( 0 );
			$bookingcal->store( $event );
		}
	}

	foreach ( $statecal->get_events( $period ) as $event ) {
		/* @var $event \AweBooking\Calendar\Event\Core\State_Event */
		if ( $event->get_state() === Constants::STATE_BOOKING ) {
			$event->set_value( Constants::STATE_AVAILABLE );
			$statecal->store( $event );
		}
	}

	return true;
}
