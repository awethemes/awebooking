<?php
namespace AweBooking\Calendar\Event;

use AweBooking\Support\Collection;
use AweBooking\Calendar\Period\Period;

class Itemizer {
	/**
	 * The events to itemizer.
	 *
	 * @var \AweBooking\Calendar\Event\Events
	 */
	protected $events;

	/**
	 * The default value for missing item.
	 *
	 * @var integer
	 */
	protected $default_value;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Calendar\Event\Events $events        The events.
	 * @param integer                           $default_value The default value.
	 */
	public function __construct( Events $events, $default_value = 0 ) {
		$this->events = $events;
		$this->default_value = (int) $default_value;
	}

	/**
	 * Transforms events in a breakdown of days with associated values.
	 *
	 * @param  \AweBooking\Calendar\Period\Period $period The period.
	 * @return array
	 */
	public function itemize( Period $period ) {
		// First, itemize all given events.
		$event_itemized = Collection::make( $this->events )
			->map( function( $event ) {
				return $this->perform_itemize_event( $event );
			})->collapse();

		// Itemized in the period.
		$itemized = [];

		foreach ( $period as $day ) {
			$index = $day->format( 'Y-m-d' );
			$itemized[ $index ] = $event_itemized->get( $index, $this->default_value );
		}

		return Itemized::make( $itemized );
	}

	/**
	 * Perform itemize an event.
	 *
	 * @param  \AweBooking\Calendar\Event\Event $event The event to itemize.
	 * @return array
	 */
	protected function perform_itemize_event( Event $event ) {
		$itemized = [];

		foreach ( $event->get_period() as $day ) {
			$itemized[ $day->format( 'Y-m-d' ) ] = $event->get_value();
		}

		return $itemized;
	}
}
