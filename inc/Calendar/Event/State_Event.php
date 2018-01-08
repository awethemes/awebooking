<?php
namespace AweBooking\Calendar\Event;

use AweBooking\Constants;
use AweBooking\Calendar\Resource\Resource_Interface;
use AweBooking\Calendar\Event\Exceptions\Invalid_State_Exception;

class State_Event extends Event {
	/**
	 * The state reference.
	 *
	 * @var mixed
	 */
	protected $reference;

	/**
	 * The valid states.
	 *
	 * @var array
	 */
	protected static $valid_states = [
		Constants::STATE_BOOKED,
		Constants::STATE_PENDING,
		Constants::STATE_AVAILABLE,
		Constants::STATE_UNAVAILABLE,
	];

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
	 * Set the event value.
	 *
	 * @param  int $value The event value.
	 * @return $this
	 *
	 * @throws Invalid_State_Exception
	 */
	public function set_value( $value ) {
		if ( ! in_array( $value, static::$valid_states ) ) {
			throw new Invalid_State_Exception( 'Invalid state' );
		}

		return parent::set_value( $value );
	}

	/**
	 * Get the reference.
	 *
	 * @return mixed
	 */
	public function get_reference() {
		return $this->reference;
	}

	/**
	 * Set the reference.
	 *
	 * "Reference" can be a booking or whaterver.
	 *
	 * @param mixed $reference The reference.
	 */
	public function set_reference( $reference ) {
		$this->reference = $reference;

		return $this;
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
