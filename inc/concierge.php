<?php

use AweBooking\Constants;
use AweBooking\Model\Room;
use AweBooking\Model\Pricing\Rate;
use AweBooking\Model\Common\Timespan;
use AweBooking\Calendar\Calendar;
use AweBooking\Calendar\Resource\Resource;
use AweBooking\Calendar\Resource\Resources;
use AweBooking\Calendar\Resource\Resource_Interface;
use AweBooking\Calendar\Provider\Cached_Provider;
use AweBooking\Calendar\Provider\Provider_Interface;
use AweBooking\Calendar\Finder\Finder;
use AweBooking\Calendar\Finder\State_Finder;
use AweBooking\Reservation\Request;
use AweBooking\Reservation\Constraints\Night_Stay_Constraint;
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
	$itemized = abrs_calendar( $rate, 'pricing', true )
		->get_events( $timespan->to_period( Constants::GL_NIGHTLY ) )
		->itemize();

	return abrs_collect( $itemized )->transform( function( $a ) {
		return abrs_decimal_raw( $a )->as_numeric();
	});
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
	if ( ! $rate instanceof Rate && ! $rate = abrs_get_rate( $rate ) ) {
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
 * Returns list of operations.
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
 * Filter given rates by request.
 *
 * @param  array|int $rates       The rates.
 * @param  Request   $request      The reservation request.
 * @param  array     $constraints  Array of constraints.
 * @return \AweBooking\Calendar\Resource\Resources
 */
function abrs_filter_rates( $rates, Request $request, $constraints = [] ) {
	$resources = abrs_collect( $rates )
		->transform( 'abrs_resource_rate' )
		->filter( /* Remove empty items */ )
		->each(function( $r ) use ( $request ) {
			$r->set_constraints( abrs_build_rate_constraints( $r->get_reference(), $request ) );
		})->all();

	$response = ( new Finder( $resources ) )
		->callback( '_abrs_filter_rates_callback' )
		->using( apply_filters( 'awebooking/filter_rates_constraints', $constraints, $resources, $request ) )
		->find( $request->get_timespan()->to_period( Constants::GL_NIGHTLY ) );

	return apply_filters( 'awebooking/filter_rates_response', $response, $request, $resources );
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

	if ( $effective_date && abrs_date_time( 'today' ) < abrs_date_time( $effective_date ) ) {
		$response->add_miss( $resource, 'rate_effective_date' );
	} elseif ( $expires_date && abrs_date_time( 'today' ) > abrs_date_time( $expires_date ) ) {
		$response->add_miss( $resource, 'rate_expired_date' );
	} else {
		$response->add_match( $resource, 'rate_valid_dates' );
	}
}

/**
 * Build the rate constraints based on reservation request.
 *
 * @param  \AweBooking\Model\Pricing\Rate  $rate    The rate instance.
 * @param  \AweBooking\Reservation\Request $request The reservation request.
 * @return array
 */
function abrs_build_rate_constraints( Rate $rate, Request $request ) {
	$constraints  = [];

	// Get rate restrictions.
	$restrictions = $rate->get_restrictions();

	if ( $restrictions['min_los'] || $restrictions['max_los'] ) {
		$constraints[] = new Night_Stay_Constraint( $rate->get_id(), $request->get_timespan(), $restrictions['min_los'], $restrictions['max_los'] );
	}

	return apply_filters( 'awebooking/rate_constraints', $constraints, $rate );
}

/**
 * Check given rooms state by request.
 *
 * @param  array|int $room        The room ID to check.
 * @param  Request   $request     The reservation request.
 * @param  array|int $states      A string or an array of states.
 * @param  array     $constraints AweBooking\Calendar\Finder\Constraint[].
 * @return \AweBooking\Calendar\Finder\Response
 */
function abrs_check_rooms( $room, Request $request, $states = Constants::STATE_AVAILABLE, $constraints = [] ) {
	$resources = abrs_collect( $room )
		->transform( 'abrs_resource_room' )
		->filter()
		->all();

	$response = ( new State_Finder( $resources, abrs_calendar_provider( 'state', $resources, true ) ) )
		->only( is_array( $states ) ? $states : [ $states ] )
		->using( apply_filters( 'awebooking/check_rooms_constraints', $constraints, $resources, $request, $states ) )
		->find( $request->get_timespan()->to_period( Constants::GL_NIGHTLY ) );

	return apply_filters( 'awebooking/check_room_state_response', $response, $request, $resources, $states );
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
 * Gets the calendar resource.
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

	if ( $resource instanceof Room ) {
		return abrs_resource_room( $resource );
	} elseif ( $resource instanceof Rate ) {
		return abrs_resource_rate( $resource );
	}

	return new Resource( (int) $resource, (int) $value );
}

/**
 * Returns a resource of room.
 *
 * @param  mixed $room The room ID.
 * @return \AweBooking\Calendar\Resource\Resource_Interface|null
 */
function abrs_resource_room( $room ) {
	$room = ( ! $room instanceof Room ) ? abrs_get_room( $room ) : $room;

	// Leave if room not found.
	if ( empty( $room ) ) {
		return;
	}

	// By default rooms in awebooking is alway available
	// but you can apply filters to here to change that.
	$resource = new Resource( $room->get_id(),
		apply_filters( 'awebooking/default_room_state', Constants::STATE_AVAILABLE )
	);

	$resource->set_reference( $room );
	$resource->set_title( $room->get( 'name' ) );

	return apply_filters( 'awebooking/calendar_room_resource', $resource, $room );
}

/**
 * Returns a resource rate.
 *
 * @param  mixed $rate The rate ID.
 * @return \AweBooking\Calendar\Resource\Resource_Interface|null
 */
function abrs_resource_rate( $rate ) {
	$rate = ( ! $rate instanceof Rate ) ? abrs_get_rate( $rate ) : $rate;

	// Leave if rate not found.
	if ( empty( $rate ) ) {
		return;
	}

	// In calendar we store as resource integer,
	// so we need convert rate amount to int value (e.g 10.9 -> 1090).
	$resource = new Resource( $rate->get_id(),
		abrs_decimal( $rate->get_rack_rate() )->as_raw_value()
	);

	$resource->set_reference( $rate );
	$resource->set_title( $rate->get_name() );

	return apply_filters( 'awebooking/calendar_rate_resource', $resource, $rate );
}
