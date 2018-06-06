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
			return abrs_redirector()->to( $availability_url );
		}

		// Redirect to the checkout page if requested
		// or in case the "booking" page is not setup yet.
		if ( $this->is_redirect_to_checkout( $request ) ) {
			return abrs_redirector()->to( abrs_get_page_permalink( 'checkout' ) );
		}

		if ( $request->has( '_continue_reservation' ) ) {
			return abrs_redirector()->to( $availability_url );
		}

		abrs_add_notice( esc_html__( 'Room has been successfully added to your reservation!', 'awebooking' ), 'success' );

		return abrs_redirector()->to( abrs_get_page_permalink( 'booking' ) );
	}

	/**
	 * Determines if redirect to the checkout page or not.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return bool
	 */
	protected function is_redirect_to_checkout( Request $request ) {
		// Redirect the checkout page in case requested or the reservation mode is "single_room".
		if ( $request->has( '_redirect_checkout' ) || 'single_room' === abrs_get_option( 'reservation_mode', 'multiple_room' ) ) {
			return true;
		}

		// Or the booking page is not set yet.
		if ( ! abrs_get_page_id( 'booking' ) ) {
			return true;
		}

		return false;
	}
}
