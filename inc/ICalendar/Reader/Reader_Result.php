<?php
namespace AweBooking\ICalendar\Reader;

use AweBooking\ICalendar\Event;
use AweBooking\Support\Collection;

class Reader_Result {
	/**
	 * The reader type.
	 *
	 * Could be: "ics", "ical", "vcal" or "xcal".
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * The property name.
	 *
	 * @var string
	 */
	protected $property;

	/**
	 * A collection of events.
	 *
	 * @var Collection
	 */
	protected $events;

	/**
	 * Create the result.
	 *
	 * @param string $property The property name.
	 * @param string $type     The type of property.
	 */
	public function __construct( $property, $type = 'ical' ) {
		$this->type     = $type;
		$this->property = $property;
		$this->events   = new Collection;
	}

	/**
	 * Get the type.
	 *
	 * @return stirng
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Get the property.
	 *
	 * @return string
	 */
	public function get_property() {
		return $this->property;
	}

	/**
	 * Get the property name.
	 *
	 * @return string
	 */
	public function get_property_name() {
		$pieces = explode( '//', $this->property );

		return isset( $pieces[1] ) ? trim( $pieces[1] ) : $this->property;
	}

	/**
	 * Get the events collection.
	 *
	 * @return Collection
	 */
	public function get_events() {
		return $this->events;
	}

	/**
	 * Add a event into result.
	 *
	 * @param Event $event The event instance.
	 */
	public function add_event( Event $event ) {
		$this->events->push( $event );
	}

	/**
	 * Determines is empty events.
	 *
	 * @return boolean
	 */
	public function is_empty() {
		return $this->events->isEmpty();
	}

	/**
	 * Determines is current result was from iCalendar.
	 *
	 * @return boolean
	 */
	public function is_icalendar() {
		return in_array( $this->type, [ 'ical', 'ics' ] );
	}
}
