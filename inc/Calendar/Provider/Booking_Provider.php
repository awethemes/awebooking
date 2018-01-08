<?php
namespace AweBooking\Calendar\Provider;

use AweBooking\Factory;
use AweBooking\Calendar\Event\Booking_Event;
use AweBooking\Calendar\Resource\Resource_Interface;
use Roomify\Bat\Event\Event as BAT_Event;

class Booking_Provider extends WP_Provider {
	/**
	 * Constructor.
	 *
	 * @param Resource_Collection|array $resources The resources to get events.
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
		$booking = Factory::get_booking( $raw_event->getValue() );

		return new Booking_Event( $resource, $raw_event->getStartDate(), $raw_event->getEndDate(), $booking );
	}
}
