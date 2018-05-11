<?php
namespace AweBooking\Frontend\Controllers;

use Awethemes\Http\Request;
use AweBooking\Reservation\Reservation;
use AweBooking\Reservation\Room_Stay\Room_Rate;
use AweBooking\Component\Http\Exceptions\ValidationFailedException;

class Reservation_Controller {
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
		$this->reservation = $reservation;
	}

	/**
	 * Handle book a room from request.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response
	 *
	 * @throws ValidationFailedException
	 */
	public function book( Request $request ) {
		if ( ! $request->filled( 'room_type', 'check_in', 'check_out' ) ) {
			throw new ValidationFailedException( esc_html__( 'Invalid request parameters, please try again.', 'awebooking' ) );
		}

		// Create the reservation request.
		$res_request = abrs_create_res_request( $request );
		if ( is_wp_error( $res_request ) ) {
			throw new ValidationFailedException( $res_request->get_error_message() );
		}

		// Sets the res request.
		$res_request['room_type'] = $request->get( 'room_type' );
		$res_request['rate_plan'] = $request->get( 'rate_plan' );

		$added = $this->reservation->add_room_stay( $res_request,
			$request->get( 'room_type' ), $request->get( 'rate_plan' )
		);

		dd( $added );

		return awebooking( 'redirector' )->back();
	}
}
