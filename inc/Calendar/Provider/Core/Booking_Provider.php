<?php

namespace AweBooking\Calendar\Provider\Core;

use AweBooking\Calendar\Provider\DB_Provider;
use AweBooking\Calendar\Event\Core\Booking_Event;
use AweBooking\Calendar\Resource\Resource_Interface;
use Roomify\Bat\Event\Event as BAT_Event;

class Booking_Provider extends DB_Provider {
	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Calendar\Resource\Resources|array $resources The resources to get events.
	 */
	public function __construct( $resources ) {
		parent::__construct( $resources, 'awebooking_booking', 'room_id' );
	}

	/**
	 * Transform the BAT_Event to the AweBooking Calendar Event.
	 *
	 * @param  BAT_Event          $raw_event The BAT event.
	 * @param  Resource_Interface $resource  The mapping resource.
	 * @return \AweBooking\Calendar\Event\Event_Interface
	 */
	protected function transform_calendar_event( BAT_Event $raw_event, Resource_Interface $resource ) {
		return new Booking_Event( $resource, $raw_event->getStartDate(), $raw_event->getEndDate(), $raw_event->getValue() );
	}
}
