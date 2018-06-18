<?php
namespace AweBooking\Frontend\Controllers;

use Awethemes\Http\Request;
use AweBooking\Reservation\Reservation;
use AweBooking\Reservation\Url_Generator;
use AweBooking\Component\Routing\Redirector;

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
	 * @return mixed
	 */
	public function book( Request $request, Redirector $redirector ) {
		if ( ! $request->filled( 'room_type', 'check_in', 'check_out' ) ) {
			abrs_add_notice( 'Invalid request parameters, please try again.', 'error' );
			return $redirector->back();
		}

		// Create the reservation request.
		$res_request = abrs_create_res_request( $request );

		if ( is_null( $res_request ) || is_wp_error( $res_request ) ) {
			return $redirector->back();
		}

		// Create the availability url.
		$availability_url = ( new Url_Generator( $res_request ) )
			->get_availability_url();

		try {
			$item = $this->reservation->add_room_stay( $res_request,
				$request->get( 'room_type' ), $request->get( 'rate_plan' )
			);
		} catch ( \Exception $e ) {
			abrs_add_notice( $e->getMessage(), 'error' );

			// Redirect back to the search availability page.
			return $redirector->to( $availability_url );
		}

		// Continue the reservation.
		if ( $this->is_continue_reservation( $request ) ) {
			return $redirector->to( add_query_arg( 'res', $res_request->get_hash(), $availability_url ) );
		}

		return $redirector->to( abrs_get_page_permalink( 'checkout' ) );
	}

	/**
	 * Determines if need redirect to the checkout page or not.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return bool
	 */
	protected function is_continue_reservation( Request $request ) {
		// Redirect the checkout page in case requested or the reservation mode is "multiple_room".
		if ( $request->has( '_continue_reservation' ) ) {
			return true;
		}

		if ( 'multiple_room' === abrs_get_option( 'reservation_mode' ) ) {
			return true;
		}

		return false;
	}
}
