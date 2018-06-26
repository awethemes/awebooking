<?php

namespace AweBooking\Calendar\Event\Core;

use AweBooking\Constants;
use AweBooking\Calendar\Event\Event;
use AweBooking\Calendar\Event\With_Only_Days;
use AweBooking\Calendar\Resource\Resource_Interface;

class State_Event extends Event {
	use With_Only_Days;

	/**
	 * Create an event.
	 *
	 * @param Resource_Interface $resource   The resource implementation.
	 * @param \DateTime|string   $start_date The start date of the event.
	 * @param \DateTime|string   $end_date   The end date of the event.
	 * @param int                $value      The state represent for this event.
	 *
	 * @throws \LogicException
	 */
	public function __construct( Resource_Interface $resource, $start_date, $end_date, $value = Constants::STATE_AVAILABLE ) {
		parent::__construct( $resource, $start_date, $end_date, $value );
	}

	/**
	 * Set the event state.
	 *
	 * @param  int $state The event state.
	 *
	 * @return $this
	 */
	public function set_state( $state ) {
		return $this->set_value( (int) $state );
	}

	/**
	 * Get the event state.
	 *
	 * @return int
	 */
	public function get_state() {
		return (int) $this->get_value();
	}
}
