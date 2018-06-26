<?php

namespace AweBooking\Calendar\Event\Core;

use AweBooking\Calendar\Event\Event;
use AweBooking\Calendar\Event\With_Only_Days;
use AweBooking\Calendar\Resource\Resource_Interface;

class Booking_Event extends Event {
	use With_Only_Days;

	/**
	 * Create an event.
	 *
	 * @param Resource_Interface $resource   The resource implementation.
	 * @param \DateTime|string   $start_date The start date of the event.
	 * @param \DateTime|string   $end_date   The end date of the event.
	 * @param int                $booking    The booking represent for this event.
	 *
	 * @throws \LogicException
	 */
	public function __construct( Resource_Interface $resource, $start_date, $end_date, $booking = 0 ) {
		parent::__construct( $resource, $start_date, $end_date, $booking );
	}
}
