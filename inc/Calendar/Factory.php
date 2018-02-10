<?php
namespace AweBooking\Calendar;

use AweBooking\Calendar\Calendar;
use AweBooking\Calendar\Resource\Resource;
use AweBooking\Calendar\Provider\State_Provider;
use AweBooking\Calendar\Provider\Booking_Provider;

class Factory {
	/**
	 * Create the state calendar by given a room_unit.
	 *
	 * @param  \AweBooking\Model\Room|init $room_unit The room unit.
	 * @return \AweBooking\Calendar\Calendar
	 */
	public function create_state_calendar( $room_unit ) {
		$resource = static::fillter_resource( $room_unit );

		return new Calendar( $resource, new State_Provider( [ $resource ] ) );
	}

	/**
	 * Create the state calendar by given a room_unit.
	 *
	 * @param  \AweBooking\Model\Room|init $room_unit The room unit.
	 * @return \AweBooking\Calendar\Calendar
	 */
	public function create_booking_calendar( $room_unit ) {
		$resource = static::fillter_resource( $room_unit );

		return new Calendar( $resource, new Booking_Provider( [ $resource ] ) );
	}

	/**
	 * Fillter the calendar resource.
	 *
	 * @param  mixed $resource The resource.
	 * @return int
	 */
	protected static function fillter_resource( $resource ) {
		if ( $resource instanceof Resource ) {
			return $resource;
		}

		$resource = method_exists( $resource, 'get_id' )
			? $resource->get_id()
			: absint( $resource );

		return new Resource( $resource );
	}
}
