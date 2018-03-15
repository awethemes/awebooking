<?php
namespace AweBooking\Calendar\Event;

use AweBooking\Constants;
use AweBooking\Calendar\Resource\Resource_Interface;

class State_Event extends Event {
	/**
	 * Create an event.
	 *
	 * @param Resource_Interface $resource   The resource implementation.
	 * @param DateTime|string    $start_date The start date of the event.
	 * @param DateTime|string    $end_date   The end date of the event.
	 * @param int                $value      The state represent for this event.
	 *
	 * @throws \LogicException
	 */
	public function __construct( Resource_Interface $resource, $start_date, $end_date, $value = Constants::STATE_AVAILABLE ) {
		parent::__construct( $resource, $start_date, $end_date, $value );
	}

	/**
	 * Determines if current event is available state.
	 *
	 * @return bool
	 */
	public function is_available_state() {
		return (int) $this->get_value() === Constants::STATE_AVAILABLE;
	}

	/**
	 * Determines if current event is unavailable state.
	 *
	 * @return bool
	 */
	public function is_unavailable_state() {
		return (int) $this->get_value() === Constants::STATE_UNAVAILABLE;
	}

	/**
	 * Determines if current event is pending state.
	 *
	 * @return bool
	 */
	public function is_pending_state() {
		return (int) $this->get_value() === Constants::STATE_PENDING;
	}

	/**
	 * Determines if current event is booked state.
	 *
	 * @return bool
	 */
	public function is_booked_state() {
		return (int) $this->get_value() === Constants::STATE_BOOKED;
	}
}
