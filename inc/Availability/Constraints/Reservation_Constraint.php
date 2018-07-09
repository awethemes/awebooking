<?php

namespace AweBooking\Availability\Constraints;

use AweBooking\Reservation\Reservation;
use AweBooking\Calendar\Finder\Response;

class Reservation_Constraint extends Constraint {
	/**
	 * The reservation instance.
	 *
	 * @var \AweBooking\Reservation\Reservation
	 */
	protected $reservation;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Reservation\Reservation $reservation The reservation instance.
	 */
	public function __construct( Reservation $reservation ) {
		$this->reservation  = $reservation;
	}

	/**
	 * {@inheritdoc}
	 */
	public function apply( Response $response ) {
		// No rooms to reject.
		if ( ( ! $res_request = $this->reservation->resolve_res_request() ) ||
			 ( ! $booked_rooms = $this->reservation->get_booked_rooms() ) ) {
			return;
		}

		foreach ( $response->get_included() as $resource => $include ) {
			if ( in_array( $resource, $booked_rooms ) ) {
				$response->reject( $include['resource'], Response::CONSTRAINT, $this );
			}
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function as_string() {
		return esc_html__( 'Booked in the current session.', 'awebooking' );
	}
}
