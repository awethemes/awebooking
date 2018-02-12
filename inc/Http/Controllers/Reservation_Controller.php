<?php
namespace AweBooking\Http\Controllers;

use AweBooking\Factory;
use AweBooking\Model\Rate;
use AweBooking\Model\Guest;
use AweBooking\Reservation\Creator;
use AweBooking\Reservation\Reservation;
use AweBooking\Reservation\Url_Generator;
use Awethemes\Http\Request;
use Illuminate\Support\Arr;

class Reservation_Controller extends Controller {
	/**
	 * The reservation creator.
	 *
	 * @var \AweBooking\Reservation\Creator
	 */
	protected $creator;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Reservation\Creator $creator The creator instance.
	 */
	public function __construct( Creator $creator ) {
		$this->creator = $creator;
	}

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

		// Get the submited room-type.
		$request_room_type = Arr::first( array_keys( (array) $request->submit ) );

		$url_generator = new Url_Generator( awebooking()->get_instance(), $reservation );

		return $this->redirect()->to(
			$url_generator->get_search_link( new Guest( 1 ), true )
		);
	}
}
