<?php
namespace AweBooking\Calendar;

use AweBooking\Calendar\Calendar;
use AweBooking\Calendar\Resource\Resource;
use AweBooking\Calendar\Provider\State_Provider;
use AweBooking\Calendar\Provider\Booking_Provider;
use AweBooking\Calendar\Provider\Pricing_Provider;
use AweBooking\Calendar\Provider\Provider_Interface;
use AweBooking\Support\Decimal;

class Factory {
	/**
	 * Create the state calendar by given a room_unit.
	 *
	 * @param  init $room_unit The room unit.
	 * @return \AweBooking\Calendar\Calendar
	 */
	public function create_state_calendar( $room_unit ) {
		$resource = static::fillter_resource( $room_unit );

		return new Calendar( $resource, new State_Provider( $resource ) );
	}

	/**
	 * Create the state calendar by given a room_unit.
	 *
	 * @param  init $room_unit The room unit.
	 * @return \AweBooking\Calendar\Calendar
	 */
	public function create_booking_calendar( $room_unit ) {
		$resource = static::fillter_resource( $room_unit );

		return new Calendar( $resource, new Booking_Provider( $resource ) );
	}

	/**
	 * Create the state calendar by given a room_unit.
	 *
	 * @param  init                                                  $rate        The rate unit.
	 * @param  \AweBooking\Support\Decimal|int                       $base_amount The rate base amount.
	 * @param  \AweBooking\Calendar\Provider\Provider_Interface|null $provider    Optional, the calendar provider.
	 * @return \AweBooking\Calendar\Calendar
	 */
	public function create_pricing_calendar( $rate, $base_amount = 0, Provider_Interface $provider = null ) {
		$resource = static::fillter_resource( $rate, $base_amount );

		if ( is_null( $provider ) ) {
			$provider = new Pricing_Provider( $resource );
		}

		return new Calendar( $resource, $provider );
	}

	/**
	 * Fillter the calendar resource.
	 *
	 * @param  mixed $resource      The resource.
	 * @param  int   $default_value The default value for resource.
	 * @return int
	 */
	protected static function fillter_resource( $resource, $default_value = 0 ) {
		if ( $resource instanceof Resource ) {
			return $resource;
		}

		$resource = method_exists( $resource, 'get_id' )
			? $resource->get_id()
			: absint( $resource );

		$default_value = ( $default_value instanceof Decimal )
			? $default_value->as_raw_value()
			: (int) $default_value;

		return new Resource( $resource, $default_value );
	}
}
