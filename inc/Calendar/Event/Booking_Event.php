<?php
namespace AweBooking\Calendar\Event;

use AweBooking\Factory;
use AweBooking\Model\Booking;
use AweBooking\Calendar\Resource\Resource_Interface;

class Booking_Event extends Event {
	/**
	 * Create an event.
	 *
	 * @param Resource_Interface $resource   The resource implementation.
	 * @param DateTime|string    $start_date The start date of the event.
	 * @param DateTime|string    $end_date   The end date of the event.
	 * @param Booking|int        $booking    The booking represent for this event.
	 *
	 * @throws \LogicException
	 */
	public function __construct( Resource_Interface $resource, $start_date, $end_date, $booking = 0 ) {
		parent::__construct( $resource, $start_date, $end_date, $booking );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_value() {
		return $this->value->get_id();
	}

	/**
	 * {@inheritdoc}
	 */
	public function set_value( $value ) {
		$this->value = $value instanceof Booking ? $value : Factory::get_booking( $value );

		return $this;
	}

	/**
	 * Get Booking represent for this event.
	 *
	 * @return \AweBooking\Model\Booking
	 */
	public function get_booking() {
		return $this->value;
	}
}
