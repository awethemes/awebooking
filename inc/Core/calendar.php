<?php

use AweBooking\Constants;
use AweBooking\Model\Room;
use AweBooking\Calendar\Calendar;
use AweBooking\Calendar\Resource\Resource;
use AweBooking\Calendar\Resource\Resource_Interface;
use AweBooking\Calendar\Provider\Provider_Interface;
use AweBooking\Calendar\Provider\Cached_Provider;
use AweBooking\Model\Pricing\Contracts\Rate_Interval;
use AweBooking\Support\Collection;

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
		$providers = apply_filters( 'abrs_calendar_providers_classmap', [
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

	$resource = is_array( $resource )
		? array_map( 'abrs_filter_resource', $resource )
		: [ abrs_filter_resource( $resource ) ];

	// Create the provider instance.
	$instance = new $providers[ $provider ]( $resource );

	// Wrap in cached provider.
	if ( $cached ) {
		$instance = new Cached_Provider( $instance );
	}

	return apply_filters( 'abrs_calendar_provider', $instance, $provider, $resource, $cached );
}

/**
 * Gets the calendar resource.
 *
 * @param  mixed $resource The resource ID or model represent of resource (Room, Rate_Interval, etc.).
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
	}

	if ( $resource instanceof Rate_Interval ) {
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
		return null;
	}

	// By default rooms in awebooking is alway available
	// but you can apply filters to here to change that.
	$resource = new Resource( $room->get_id(),
		apply_filters( 'abrs_default_room_state', Constants::STATE_AVAILABLE )
	);

	$resource->set_reference( $room );
	$resource->set_title( $room->get( 'name' ) );

	return apply_filters( 'abrs_calendar_room_resource', $resource, $room );
}

/**
 * Returns a resource of rate.
 *
 * @param  mixed $rate The rate interval ID.
 * @return \AweBooking\Calendar\Resource\Resource_Interface|null
 */
function abrs_resource_rate( $rate ) {
	$rate = ( ! $rate instanceof Rate_Interval ) ? abrs_get_rate_interval( $rate ) : $rate;

	// Leave if rate not found.
	if ( empty( $rate ) ) {
		return null;
	}

	// In calendar we store as resource integer,
	// so we need convert rate amount to int value (e.g 10.9 -> 1090).
	$resource = new Resource( $rate->get_id(),
		abrs_decimal( $rate->get_rack_rate() )->as_raw_value()
	);

	$resource->set_reference( $rate );
	$resource->set_title( $rate->get_name() );

	return apply_filters( 'abrs_calendar_rate_resource', $resource, $rate );
}
