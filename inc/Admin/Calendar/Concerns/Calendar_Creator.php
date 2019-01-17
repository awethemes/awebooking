<?php

namespace AweBooking\Admin\Calendar\Concerns;

use AweBooking\Calendar\Calendar;
use AweBooking\Calendar\Scheduler;
use AweBooking\Calendar\Resource\Resource;
use AweBooking\Calendar\Resource\Resources;
use AweBooking\Calendar\Provider\Provider_Interface;

trait Calendar_Creator {
	/**
	 * Create room resources.
	 *
	 * @param  \AweBooking\Support\Collection|array $rooms The rooms.
	 * @param  int                                  $state Default resource state.
	 *
	 * @return \AweBooking\Calendar\Resource\Resources
	 */
	protected function create_room_resources( $rooms, $state = 0 ) {
		$resources = [];

		foreach ( $rooms as $room ) {
			$resource = ( new Resource( $room->get_id(), $state ) )
				->set_title( $room->get_name() )
				->set_reference( $room );

			$resources[ $room->get_id() ] = $resource;
		}

		return Resources::make( $resources );
	}

	/**
	 * Create rate resources.
	 *
	 * @param  \AweBooking\Support\Collection|array $rates The rates.
	 *
	 * @return \AweBooking\Calendar\Resource\Resources
	 */
	protected function create_rate_resources( $rates ) {
		$resources = [];

		foreach ( $rates as $rate ) {
			// Because the Calendar works only with integer,
			// so we need get the raw value from amount.
			$amount = abrs_decimal( $rate->get_rack_rate() )->as_raw_value();

			$resource = ( new Resource( $rate->get_id(), $amount ) )
				->set_reference( $rate )
				->set_title( $rate->get_name() );

			$resources[ $rate->get_id() ] = $resource;
		}

		return Resources::make( $resources );
	}

	/**
	 * Create scheduler by given a resources.
	 *
	 * @param  \AweBooking\Calendar\Resource\Resources          $resources The resources.
	 * @param  \AweBooking\Calendar\Provider\Provider_Interface $provider  The calendar provider.
	 *
	 * @return \AweBooking\Calendar\Scheduler
	 */
	protected function create_scheduler_for( Resources $resources, Provider_Interface $provider ) {
		$calendars = [];

		foreach ( $resources as $resource ) {
			$calendar = new Calendar( $resource, $provider );

			$calendar->set_name( $resource->get_title() );
			$calendar->set_description( $resource->get_description() );

			$calendars[] = $calendar;
		}

		return Scheduler::make( $calendars );
	}
}
