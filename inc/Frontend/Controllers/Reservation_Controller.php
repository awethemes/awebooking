<?php
namespace AweBooking\Frontend\Controllers;

use Awethemes\Http\Request;
use AweBooking\Reservation\Reservation;
use AweBooking\Reservation\Room_Stay\Room_Rate;

class Reservation_Controller {
	protected $reservation;

	public function __construct( Reservation $reservation ) {
		$this->reservation = $reservation;
	}

	/**
	 * Handle book a room from request.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response
	 */
	public function book( Request $request ) {
		$res = $this->reservation;

		if ( ! $request->filled( 'room_type', 'check_in', 'check_out' ) ) {
			return 0;
		}

		$room_type = abrs_get_room_type( $request->get( 'room_type' ) );
		if ( empty( $room_type ) ) {
			return 1;
		}

		// Create the reservation request.
		$res_request = abrs_create_res_request( $request );

		if ( is_wp_error( $res_request ) ) {
			return $res_request;
		}

		$added = $res->add_room_stay( $room_type, null, $res_request );

		return awebooking( 'redirector' )->back();
	}
}
