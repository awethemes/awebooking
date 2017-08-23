<?php
namespace AweBooking\Booking\Events;

use DateTime;
use AweBooking\Hotel\Room;
use Roomify\Bat\Event\Event;
use AweBooking\Support\Carbonate;

class Room_Booking extends Event {
	/**
	 * Booking event constructor.
	 *
	 * @param Room     $the_room   The room instance.
	 * @param DateTime $start_date Start of date of booking event.
	 * @param DateTime $end_date   End of date of  booking event.
	 * @param bool     $booking_id //.
	 */
	public function __construct( Room $the_room, DateTime $start_date, DateTime $end_date, $booking_id ) {
		$this->unit       = $the_room;
		$this->unit_id    = $the_room->getUnitId();
		$this->value      = $booking_id;
		$this->end_date   = Carbonate::instance( $end_date );
		$this->start_date = Carbonate::instance( $start_date );
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
