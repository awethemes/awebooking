<?php
namespace AweBooking\Reservation\Searcher;

use AweBooking\Constants;
use AweBooking\Model\Stay;
use AweBooking\Model\Room;
use AweBooking\Model\Room_Type;
use AweBooking\Calendar\Calendar;
use AweBooking\Calendar\Scheduler;
use AweBooking\Calendar\Resource\Resource;
use AweBooking\Calendar\Event\State_Event;
use AweBooking\Calendar\Provider\Cached_Provider;
use AweBooking\Calendar\Provider\State_Provider;
use AweBooking\Support\Utils as U;

class Checker {
	/**
	 * Perform the check availability.
	 *
	 * @param  \AweBooking\Model\Room_Type $room_type   The Room_Type model.
	 * @param  \AweBooking\Model\Stay      $stay        The Stay model.
	 * @param  array                       $constraints The constraints.
	 * @return \AweBooking\Reservation\Searcher\Availability
	 */
	public function check( Room_Type $room_type, Stay $stay, array $constraints = [] ) {
		$rooms     = U::collect( $room_type->get_rooms() );
		$resources = $this->map_units_to_resources( $rooms );

		$period    = $stay->to_period();
		$scheduler = $this->create_scheduler( $resources );

		// Create the availability.
		$availability = new Availability( $room_type, $stay, $rooms );

		// Loop through each room-calendar and check the availability.
		foreach ( $scheduler as $calendar ) {
			$events = $calendar->get_events( $period );
			$room_unit = $this->retrieve_unit_from_resource( $calendar->get_resource(), $rooms );

			if ( $this->has_unavaiable_state( $events ) ) {
				$availability->exclude( $room_unit, Reason::INVALID_STATE );
			} else {
				$availability->include( $room_unit, Reason::VALID_STATE );
			}
		}

		// Apply the constraints.
		$availability->apply_constraints( $constraints );

		return $availability;
	}

	/**
	 * Determines if given a Room is available for booking.
	 *
	 * @param  \AweBooking\Model\Room $room_unit The Room unit.
	 * @param  \AweBooking\Model\Stay $stay      The Stay instance.
	 * @return boolean
	 */
	public function is_available_for( Room $room_unit, Stay $stay ) {
		$resources = $this->map_units_to_resources(
			U::collect( [ $room_unit ] )
		);

		$calendar = $this->create_scheduler( $resources )
						 ->first();

		return ! $this->has_unavaiable_state(
			$calendar->get_events( $stay->to_period() )
		);
	}

	/**
	 * Create the availability scheduler.
	 *
	 * @param  \AweBooking\Calendar\Resources\Resource_Collection $resources The resources.
	 * @return \AweBooking\Calendar\Scheduler
	 */
	protected function create_scheduler( $resources ) {
		$provider = new Cached_Provider( new State_Provider( $resources ) );

		$calendars = $resources->map( function( $resource ) use ( $provider ) {
			return new Calendar( $resource, $provider );
		});

		return Scheduler::make( $calendars );
	}

	/**
	 * Map room-units to resource.
	 *
	 * @param  \AweBooking\Support\Collection $rooms The room units.
	 * @return \AweBooking\Support\Collection
	 */
	protected function map_units_to_resources( $rooms ) {
		return $rooms->map( function( $room ) {
			return new Resource( $room->get_id(), Constants::STATE_AVAILABLE );
		});
	}

	/**
	 * Retrieve the room-unit from resource.
	 *
	 * @param  \AweBooking\Calendar\Resources\Resource $resource The resource.
	 * @param  \AweBooking\Support\Collection          $rooms    The rooms.
	 * @return \AweBooking\Model\Room|null
	 */
	protected function retrieve_unit_from_resource( $resource, $rooms ) {
		$rooms = $rooms->keyBy( 'id' );

		return $rooms->get( $resource->get_id() );
	}

	/**
	 * Determines if an collection events have any unavailable state.
	 *
	 * @param  \AweBooking\Calendar\Event\Event_Collection $events The events collection.
	 * @return boolean
	 */
	protected function has_unavaiable_state( $events ) {
		// Reject all events is not represent for the state.
		$events = $events->reject( function( $e ) {
			return ! $e instanceof State_Event;
		});

		// If we have any "unavailable-like" events in the collect,
		// set current room_unit is not included in the availability.
		return $events->contains( function( $e ) {
			return ! $e->is_available_state();
		});
	}
}
