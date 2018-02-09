<?php
namespace AweBooking\Reservation\Searcher\Constraints;

use AweBooking\Model\Booking;
use AweBooking\Reservation\Searcher\Reason;
use AweBooking\Reservation\Searcher\Constraint;
use AweBooking\Reservation\Searcher\Availability;

class Rooms_In_Booking_Constraint implements Constraint {
	/**
	 * The booking instance.
	 *
	 * @var \AweBooking\Booking\Booking
	 */
	protected $booking;

	/**
	 * Reject rooms already in a booking.
	 *
	 * @param \AweBooking\Booking\Booking $booking The booking instance.
	 */
	public function __construct( Booking $booking ) {
		$this->booking = $booking;
	}

	/**
	 * {@inheritdoc}
	 */
	public function apply( Availability $availability ) {
		$remain_rooms = $availability->remain_rooms();

		// Leave if we have no remain rooms.
		if ( $remain_rooms->isEmpty() ) {
			return;
		}

		// List all booked rooms.
		$booked_rooms = $this->booking->get_line_items();

		// Loop througth remain_rooms and reject booking rooms.
		$remain_rooms->each( function( $item ) use ( $booked_rooms, $availability ) {
			if ( $this->room_was_booked( $item['room'], $booked_rooms, $availability->get_stay() ) ) {
				$availability->reject( $item['room'], Reason::BOOKED_ROOM );
			}
		});
	}

	/**
	 * Determines a room was booked in given booked_rooms.
	 *
	 * @param  \AweBooking\Hotel\Room         $room         The check room.
	 * @param  \AweBooking\Support\Collection $booked_rooms The booked rooms.
	 * @return boolean
	 */
	protected function room_was_booked( $room, $booked_rooms, $stay ) {
		return $booked_rooms->contains( function( $booked_room ) use ( $room, $stay ) {
			return $booked_room->get_room_id() === $room->get_id()
				&& $booked_room->check_in == $stay->get_check_in()->toDateString()
				&& $booked_room->check_out == $stay->get_check_out()->toDateString();
		});
	}
}
