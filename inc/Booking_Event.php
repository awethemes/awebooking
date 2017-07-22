<?php
namespace AweBooking;

use DateTime;
use Carbon\Carbon;
use RuntimeException;
use Roomify\Bat\Event\Event;

class Booking_Event extends Event {
	/**
	 * Booking event constructor.
	 *
	 * @param Booking  $booking    The booking instance.
	 * @param DateTime $start_date Start of date of booking event.
	 * @param DateTime $end_date   End of date of  booking event.
	 * @param bool     $clear      If true, we consider this event will be removed.
	 *
	 * @throws RuntimeException
	 */
	public function __construct( Booking $booking, DateTime $start_date, DateTime $end_date, $clear = false ) {
		$the_room = $booking->get_booking_room();

		if ( ! $the_room instanceof Room ) {
			throw new RuntimeException( 'The booking instance must be have valid booking room.' );
		}

		$this->unit       = $the_room;
		$this->unit_id    = $the_room->getUnitId();
		$this->value      = $clear ? 0 : $booking->get_id();

		$this->end_date   = Carbon::instance( $end_date );
		$this->start_date = Carbon::instance( $start_date );
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
