<?php
namespace AweBooking\Calendar\Provider;

use AweBooking\Calendar\Event\State_Event;
use AweBooking\Calendar\Resource\Resource_Interface;
use Roomify\Bat\Event\Event as BAT_Event;

class State_Provider extends WP_Provider {
	/**
	 * Constructor.
	 *
	 * @param Resource_Collection|array $resources The resources to get events.
	 */
	public function __construct( $resources ) {
		parent::__construct( $resources, 'awebooking_availability', 'room_id' );
	}

	/**
	 * Transform the BAT_Event to the AweBooking Calendar Event.
	 *
	 * @param  BAT_Event          $raw_event The BAT event.
	 * @param  Resource_Interface $resource  The mapping resource.
	 * @return \AweBooking\Calendar\Event\Event_Interface
	 */
	protected function transform_calendar_event( BAT_Event $raw_event, Resource_Interface $resource ) {
		$event = new State_Event( $resource, $raw_event->getStartDate(), $raw_event->getEndDate(), $raw_event->getValue() );

		switch ( true ) {
			case $event->is_available_state():
				$event->set_summary( esc_html_x( 'Available', 'booking state', 'awebooking' ) );
				break;

			case $event->is_unavailable_state():
				$event->set_summary( esc_html_x( 'Unavailable', 'booking state', 'awebooking' ) );
				break;

			case $event->is_pending_state():
				$event->set_summary( esc_html_x( 'Pending', 'booking state', 'awebooking' ) );
				break;

			case $event->is_booked_state():
				$event->set_summary( esc_html_x( 'Booked', 'booking state', 'awebooking' ) );
				break;
		}

		return $event;
	}
}
