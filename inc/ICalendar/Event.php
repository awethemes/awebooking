<?php
namespace AweBooking\ICalendar;

use AweBooking\Calendar\Resource\Resource;
use AweBooking\Calendar\Event\Event as Base_Event;

class Event extends Base_Event {
	/**
	 * Create an event.
	 *
	 * @param DateTime|string $start_date The start date of the event.
	 * @param DateTime|string $end_date   The end date of the event.
	 *
	 * @throws \LogicException
	 */
	public function __construct( $start_date, $end_date ) {
		$resource = new Resource( -1 );

		parent::__construct( $resource, $start_date, $end_date, 0 );
	}

	/**
	 * Determines if this event should be a "booking" event.
	 *
	 * @return string
	 */
	public function should_be_booking() {
		return ! $this->should_be_unavailable();
	}

	/**
	 * Determines if this event should be an "unavailable" state.
	 *
	 * @return boolean
	 */
	public function should_be_unavailable() {
		return in_array( strtolower( $this->get_summary() ), [ 'not available', 'unavailable' ] );
	}

	/**
	 * Magic getter method, helpful for the Collection.
	 *
	 * @param  string $property Getter property name.
	 * @return mixed
	 */
	public function __get( $property ) {
		$method = "get_{$property}";

		if ( method_exists( $this, $method ) ) {
			return $this->{$method}();
		}
	}
}
