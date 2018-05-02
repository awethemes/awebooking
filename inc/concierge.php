<?php

use AweBooking\Constants;
use AweBooking\Model\Room;
use AweBooking\Model\Pricing\Rate;
use AweBooking\Model\Common\Timespan;
use AweBooking\Finder\State_Finder;
use AweBooking\Calendar\Calendar;
use AweBooking\Calendar\Resource\Resource;
use AweBooking\Calendar\Resource\Resources;
use AweBooking\Calendar\Resource\Resource_Interface;
use AweBooking\Calendar\Provider\Cached_Provider;
use AweBooking\Calendar\Provider\Provider_Interface;
use AweBooking\Support\Collection;

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
	 * @param \AweBooking\Finder\Response       $response The finder response.
	 * @param mixed                             $room     The given room(s) to check.
	 * @param \AweBooking\Model\Common\Timespan $timespan The timespan instance.
	 * @param array                             $states   The states.
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
function abrs_retrieve_rate( $rate, Timespan $timespan ) {
	try {
		$timespan->requires_minimum_nights( 1 );
	} catch ( LogicException $e ) {
		return new WP_Error( 'timespan_error', $e->getMessage() );
	}

	// Leave if given invalid rate ID.
	if ( ! $rate instanceof Rate && ! $rate = abrs_get_rate( $rate ) ) {
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
function abrs_apply_rate( $rate, Timespan $timespan, $amount, $operation = 'replace', $options = [] ) {
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

/**
 * Create a Calendar.
 *
 * @param  mixed   $resource The resource.
 * @param  string  $provider The provider name.
 * @param  boolean $cached   If true, wrap the provider in Cached_Provider.
 * @return \AweBooking\Calendar\Calendar
 */
function abrs_calendar( $resource, $provider, $cached = false ) {
	$resource = abrs_filter_resource( $resource );

	if ( ! $provider instanceof Provider_Interface ) {
		$provider = abrs_calendar_provider( $provider, $resource, $cached );
	}

	return new Calendar( $resource, $provider );
}

/**
 * Create a calendar provider.
 *
 * @param  string  $provider The provider name.
 * @param  mixed   $resource The resource.
 * @param  boolean $cached   If true, wrap the provider in Cached_Provider.
 * @return \AweBooking\Calendar\Provider\Provider_Interface
 *
 * @throws OutOfBoundsException
 */
function abrs_calendar_provider( $provider, $resource, $cached = false ) {
	static $providers;

	if ( is_null( $providers ) ) {
		$providers = apply_filters( 'awebooking/calendar_providers_classmap', [
			'state'   => \AweBooking\Calendar\Provider\Core\State_Provider::class,
			'booking' => \AweBooking\Calendar\Provider\Core\Booking_Provider::class,
			'pricing' => \AweBooking\Calendar\Provider\Core\Pricing_Provider::class,
		]);
	}

	if ( ! array_key_exists( $provider, $providers ) ) {
		throw new OutOfBoundsException( 'Invalid calendar provider' );
	}

	// Filter the resources.
	if ( $resource instanceof Collection ) {
		$resource = $resource->all();
	}

	$resource = ( is_array( $resource ) )
		? array_map( 'abrs_filter_resource', $resource )
		: [ abrs_filter_resource( $resource ) ];

	// Create the provider instance.
	$instance = new $providers[ $provider ]( $resource );

	// Wrap in cached provider.
	if ( $cached ) {
		$instance = new Cached_Provider( $instance );
	}

	return apply_filters( 'awebooking/calendar_provider', $instance, $provider, $resource, $cached );
}

/**
 * Returns a resource instance by given ID or a model.
 *
 * @param  mixed $resource The resource ID or model represent of resource (Room, Rate, etc.).
 * @param  mixed $value    The resource value.
 * @return \AweBooking\Calendar\Resource\Resource_Interface
 */
function abrs_filter_resource( $resource, $value = null ) {
	// Leave if $resource already instance of Resource_Interface.
	if ( $resource instanceof Resource_Interface ) {
		return $resource;
	}

	// Correct the resource ID & value.
	switch ( true ) {
		case ( $resource instanceof Room ):
			$id    = $resource->get_id();
			$value = Constants::STATE_AVAILABLE;
			break;

		case ( $resource instanceof Rate ):
			$id    = $resource->get_id();
			$value = abrs_decimal( $resource->get_rack_rate() )->as_raw_value();
			break;

		default:
			$id    = (int) $resource;
			$value = (int) $value;
			break;
	}

	return apply_filters( 'awebooking/calendar_resource', new Resource( $id, $value ), $resource, $value );
}
