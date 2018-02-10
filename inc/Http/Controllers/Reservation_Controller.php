<?php
namespace AweBooking\Http\Controllers;

use AweBooking\Factory;
use AweBooking\Model\Rate;
use AweBooking\Model\Guest;
use AweBooking\Reservation\Reservation;
use Illuminate\Support\Arr;
use Awethemes\Http\Request;
use AweBooking\Reservation\Url_Generator;

class Reservation_Controller extends Controller {
	/**
	 * Add a room-item in session reservation.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response
	 *
	 * @throws \RuntimeException
	 */
	public function add_item( Request $request ) {
		$request->verify_nonce( '_wpnonce', 'awebooking_reservation' );

		$reservation = awebooking( 'reservation_session' )->resolve();

		if ( is_null( $reservation ) ) {
			$this->notices( 'error', esc_html__( 'The reservation session could not found, please try again.', 'awebooking' ) );
			return $this->redirect()->back();
		}

		$url_generator = new Url_Generator( $reservation );

		// Get the submited room-type.
		$request_room_type = Arr::first( array_keys( (array) $request->submit ) );

		return $this->redirect()->to( $url_generator->get_search_link( new Guest( 1 ), true ) );
	}
}
