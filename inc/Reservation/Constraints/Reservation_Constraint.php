<?php
namespace AweBooking\Reservation\Constraints;

use AweBooking\Reservation\Reservation;
use AweBooking\Reservation\Search\Reason;
use AweBooking\Reservation\Search\Constraint;
use AweBooking\Reservation\Search\Availability;

class Reservation_Constraint implements Constraint {
	/**
	 * The reservation instance.
	 *
	 * @var \AweBooking\Reservation\Reservation
	 */
	protected $reservation;

	/**
	 * Reject selected rooms in a reservation session.
	 *
	 * @param \AweBooking\Reservation\Reservation $reservation The reservation instance.
	 */
	public function __construct( Reservation $reservation ) {
		$this->reservation = $reservation;
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

		// Loop througth remain_rooms and reject reservation rooms.
		$remain_rooms->each( function( $item ) use ( $availability ) {
			if ( $this->reservation->has_room( $item['room'] ) ) {
				$availability->reject( $item['room'], Reason::SELECTED_ROOM );
			}
		});
	}
}
