<?php

namespace AweBooking\Calendar\Event;

use AweBooking\Calendar\Period\Iterator_Period;

class Itemizer {
	/**
	 * The events to itemizer.
	 *
	 * @var \AweBooking\Calendar\Event\Events
	 */
	protected $events;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Calendar\Event\Events $events The events.
	 */
	public function __construct( Events $events ) {
		$this->events = $events;
	}

	/**
	 * Transforms events in a breakdown of days with associated values.
	 *
	 * @return \AweBooking\Calendar\Event\Itemized
	 */
	public function itemize() {
		return Itemized::make( $this->events )
			->transform( function( $event ) {
				return $this->perform_itemize_event( $event );
			})->collapse();
	}

	/**
	 * Perform itemize an event.
	 *
	 * @param  \AweBooking\Calendar\Event\Event $event The event to itemize.
	 * @return array
	 */
	protected function perform_itemize_event( Event $event ) {
		$period = new Iterator_Period( $event->get_start_date(), $event->get_end_date() );

		$itemized = [];

		foreach ( $period as $day ) {
			$itemized[ $day->format( 'Y-m-d' ) ] = $event->get_value();
		}

		return $itemized;
	}
}
