<?php
namespace AweBooking\Reservation\Searcher;

use AweBooking\Constants;
use AweBooking\Model\Room;
use AweBooking\Model\Room_Type;
use AweBooking\Model\Common\Timespan;
use AweBooking\Support\Utils as U;

use AweBooking\Calendar\Finder\Finder;
use AweBooking\Calendar\Resource\Resource;
use AweBooking\Calendar\Provider\Core\State_Provider;
use AweBooking\Calendar\Provider\Cached_Provider;

class Checker {
	/**
	 * Check availability of a given room-type.
	 *
	 * @param  \AweBooking\Model\Room_Type       $room_type   The room_type.
	 * @param  \AweBooking\Model\Common\Timespan $timespan    The timespan.
	 * @param  array                             $constraints The constraints.
	 * @return \AweBooking\Reservation\Searcher\Availability
	 */
	public function check( Room_Type $room_type, Timespan $timespan, $constraints = [] ) {
		$rooms = $room_type->get_rooms()->keyBy( 'id' );

		$rooms_response = $this->do_find_rooms( $rooms, $timespan, [ Constants::STATE_AVAILABLE ], $constraints );

		return new Availability( $timespan, $room_type, $rooms_response );
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
		// Transform the rooms to resources.
		$resources = abrs_collect( $rooms )->transform( function( $room ) {
			$resource = new Resource( $room->get_id(), Constants::STATE_AVAILABLE );

			$resource->set_reference( $room );
			$resource->set_title( $room->get_name() );

			return $resource;
		});

		// Create the provider.
		$provider = new Cached_Provider( new State_Provider( $resources ) );
		$provider = apply_filters( 'awebooking/reservation/rooms_finder_provider', $provider, $resources );

		return ( new Finder( $resources, $provider ) )
			->only( $states )
			->using( $constraints )
			->find( $timespan->to_period( Constants::GL_NIGHTLY ) );
	}
}
