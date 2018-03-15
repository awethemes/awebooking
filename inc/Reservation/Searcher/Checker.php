<?php
namespace AweBooking\Reservation\Searcher;

use AweBooking\Constants;
use AweBooking\Model\Room;
use AweBooking\Model\Room_Type;
use AweBooking\Model\Common\Timespan;
use AweBooking\Support\Utils as U;

use AweBooking\Calendar\Finder\Finder;
use AweBooking\Calendar\Resource\Resource;
use AweBooking\Calendar\Provider\State_Provider;
use AweBooking\Calendar\Provider\Cached_Provider;

class Checker {
	/**
	 * Perform the check availability.
	 *
	 * @param  \AweBooking\Model\Room_Type       $room_type The Room_Type model.
	 * @param  \AweBooking\Model\Common\Timespan $timespan  The Timespan model.
	 * @return \AweBooking\Reservation\Searcher\Availability
	 */
	public function check( Room_Type $room_type, Timespan $timespan ) {
		// Get all rooms available of room-type, index by ID.
		$rooms = $room_type->get_rooms()->keyBy( 'id' );

		// Transform the rooms to resources.
		$resonse = $this->do_find_rooms( $rooms, $timespan, Constants::STATE_AVAILABLE, [] );

		return new Availability( $room_type, $timespan, $response );
	}

	/**
	 * Perform find the rooms matching.
	 *
	 * @param  array                             $rooms       The rooms.
	 * @param  \AweBooking\Model\Common\Timespan $timespan    The timespan.
	 * @param  array|int                         $states      The states.
	 * @param  array                             $constraints The constraints.
	 * @return \AweBooking\Calendar\Finder\Response
	 */
	public function do_find_rooms( $rooms, Timespan $timespan, $states = [], $constraints = [] ) {
		$rooms = ! is_array( $rooms ) ? [ $rooms ] : $rooms;

		// Transform the rooms to resources.
		$resources = U::collect( $rooms )->transform( function( $room ) {
			return new Resource( $room->get_id(), Constants::STATE_AVAILABLE );
		});

		// Find the rooms availability.
		$provider = new Cached_Provider( new State_Provider( $resources ) );
		$provider = apply_filters( 'awebooking/reservation/rooms_finder_provider', $provider, $resources );

		return ( new Finder( $resources, $provider ) )
			->only( $states )
			->using( $constraints )
			->find( $timespan->to_period() );
	}
}
