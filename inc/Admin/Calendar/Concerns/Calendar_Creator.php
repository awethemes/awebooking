<?php
namespace AweBooking\Admin\Calendar\Concerns;

use AweBooking\Calendar\Calendar;
use AweBooking\Calendar\Scheduler;
use AweBooking\Calendar\Resource\Resource;
use AweBooking\Calendar\Resource\Resources;
use AweBooking\Calendar\Provider\Cached_Provider;
use AweBooking\Calendar\Provider\Provider_Interface;
use AweBooking\Calendar\Provider\Core\State_Provider;
use AweBooking\Calendar\Provider\Core\Pricing_Provider;
use AweBooking\Calendar\Provider\Core\Booking_Provider;

trait Calendar_Creator {
	/**
	 * Create scheduler by given a resources.
	 *
	 * @param  \AweBooking\Calendar\Resource\Resources          $resources The resources.
	 * @param  \AweBooking\Calendar\Provider\Provider_Interface $provider  The calendar provider.
	 * @return \AweBooking\Calendar\Scheduler
	 */
	protected function create_scheduler_for( Resources $resources, Provider_Interface $provider ) {
		if ( ! $provider instanceof Cached_Provider ) {
			$provider = new Cached_Provider( $provider );
		}

		$calendars = [];

		foreach ( $resources as $resource ) {
			$calendar = new Calendar( $resource, $provider );

			$calendar->set_name( $resource->get_title() );
			$calendar->set_description( $resource->get_description() );

			$calendars[] = $calendar;
		}

		return Scheduler::make( $calendars );
	}

	/**
	 * Create rate resources.
	 *
	 * @param  array|Collection $rates The rates.
	 * @return \AweBooking\Calendar\Resource\Resources
	 */
	protected function create_rate_resources( $rates ) {
		$resources = [];

		foreach ( $rates as $rate ) {
			// Because the Calendar works only with integer,
			// so we need get the raw value from amount.
			$amount = $rate->get_rack_rate()->as_raw_value();

			$resources[ $rate->get_id() ] = ( new Resource( $rate->get_id(), $amount ) )
				->set_reference( $rate )
				->set_title( $rate->get_name() );
		}

		return Resources::make( $resources );
	}

	/**
	 * Create room resources.
	 *
	 * @param  array|Collection $rooms The rooms.
	 * @param  int              $state Default resource state.
	 * @return \AweBooking\Calendar\Resource\Resources
	 */
	protected function create_room_resources( $rooms, $state = 0 ) {
		$resources = [];

		foreach ( $rooms as $room ) {
			$resources[ $room->get_id() ] = ( new Resource( $room->get_id(), $state ) )
				->set_title( $room->get_name() )
				->set_reference( $room );
		}

		return Resources::make( $resources );
	}

	/**
	 * Create the calendar provider.
	 *
	 * @param  string $provider  The provider name ['booking', 'pricing', 'state'].
	 * @param  mixed  $resources The resources.
	 * @return \AweBooking\Calendar\Provider\Provider_Interface
	 */
	protected function create_calendar_provider( $provider, $resources ) {
		// Handle resolve provider by name.
		switch ( $provider ) {
			case 'pricing':
				$provider = new Pricing_Provider( $resources );
				break;
			case 'booking':
				$provider = new Booking_Provider( $resources );
				break;
			default:
				$provider = new State_Provider( $resources );
				break;
		}

		// Wrap the provider in Cached_Provider.
		return new Cached_Provider( $provider );
	}
}
