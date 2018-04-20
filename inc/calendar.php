<?php

use AweBooking\Constants;
use AweBooking\Model\Room;
use AweBooking\Model\Pricing\Rate;
use AweBooking\Calendar\Calendar;
use AweBooking\Calendar\Resource\Resource;
use AweBooking\Calendar\Resource\Resources;
use AweBooking\Calendar\Resource\Resource_Interface;
use AweBooking\Calendar\Provider\Cached_Provider;
use AweBooking\Calendar\Provider\Provider_Interface;
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
