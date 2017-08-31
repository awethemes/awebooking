<?php
namespace AweBooking\Booking\Events;

use AweBooking\Hotel\Room;
use Roomify\Bat\Event\Event;
use AweBooking\Support\Period;
use AweBooking\Booking\Booking;

class Room_Booking extends Event {
	/**
	 * Booking event constructor.
	 *
	 * @param Room    $room_unit The room unit instance.
	 * @param Period  $period    The period of event.
	 * @param Booking $booking   The booking instance.
	 */
	public function __construct( Room $room_unit, Period $period, Booking $booking ) {
		$this->unit       = $room_unit;
		$this->unit_id    = $room_unit->getUnitId();
		$this->value      = $booking->get_id();
		$this->start_date = $period->get_start_date();
		$this->end_date   = $period->get_end_date()->subMinute();
	}

	/**
	 * Save current state into the database.
	 *
	 * @return bool
	 */
	public function save() {
		return awebooking( 'store.booking' )->storeEvent( $this );
	}
}
