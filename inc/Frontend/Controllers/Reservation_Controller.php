<?php
namespace AweBooking\Frontend\Controllers;

use Awethemes\Http\Request;
use AweBooking\Reservation\Reservation;
use AweBooking\Reservation\Url_Generator;

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
	 */
	public function book( Request $request ) {
		if ( ! $request->filled( 'room_type', 'check_in', 'check_out' ) ) {
			abrs_add_notice( 'Invalid request parameters, please try again.', 'error' );
			return abrs_redirector()->back();
		}

		// Create the reservation request.
		$res_request = abrs_create_res_request( $request );

		if ( is_null( $res_request ) || is_wp_error( $res_request ) ) {
			return abrs_redirector()->back();
		}

		$url_generator = new Url_Generator( $res_request );
		$availability_url = $url_generator->get_availability_url();

		try {
			$this->reservation->add_room_stay( $res_request, $request->get( 'room_type' ), $request->get( 'rate_plan' ) );
		} catch ( \Exception $e ) {
			abrs_add_notice( $e->getMessage(), 'error' );
			return abrs_redirector()->back( $availability_url );
		}

		abrs_add_notice( esc_html__( 'Room has been successfully added to your reservation!', 'awebooking' ), 'success' );

		// Redirect to the checkout page if requested
		// or in case the "booking" page is not setup yet.
		if ( $request->has( '_redirect_checkout' ) || ! abrs_get_page_id( 'booking' ) ) {
			return abrs_redirector()->to( abrs_get_page_permalink( 'checkout' ) );
		}

		return abrs_redirector()->to( abrs_get_page_permalink( 'booking' ) );
	}
}
