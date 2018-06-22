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
	 * The redirector.
	 *
	 * @var \AweBooking\Component\Routing\Redirector
	 */
	protected $redirector;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Reservation\Reservation      $reservation The reservation instance.
	 * @param \AweBooking\Component\Routing\Redirector $redirector  The redirector.
	 */
	public function __construct( Reservation $reservation, Redirector $redirector ) {
		$this->reservation = $reservation;
		$this->redirector  = $redirector;
	}

	/**
	 * Handle book a room from request.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return mixed
	 */
	public function book( Request $request ) {
		if ( ! $request->filled( 'room_type', 'check_in', 'check_out' ) ) {
			abrs_add_notice( 'Invalid request parameters, please try again.', 'error' );
			return $this->redirector->back();
		}

		// Create the reservation request.
		$res_request = abrs_create_res_request( $request );

		if ( is_null( $res_request ) || is_wp_error( $res_request ) ) {
			return $this->redirector->back();
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
			return $this->redirector->to( $availability_url );
		}

		// dd($this->reservation);

		// Continue the reservation.
		if ( $this->is_continue_reservation( $request ) ) {
			return $this->redirector->to( add_query_arg( 'res', $res_request->get_hash(), $availability_url ) );
		}

		return $this->redirector->to( abrs_get_page_permalink( 'checkout' ) );
	}

	/**
	 * Handle book a room from request.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @param  string                  $row_id  The row ID.
	 * @return mixed
	 */
	public function remove( Request $request, $row_id ) {
		$removed = $this->reservation->remove( $row_id );

		if ( $removed ) {
			/* translators: Item name in quotes */
			abrs_add_notice( sprintf( esc_html__( 'The room &ldquo;%s&rdquo; has been removed from your reservation', 'awebooking' ), esc_html( $removed->get_name() ) ) );
		}

		return $this->redirector->back(
			add_query_arg( 'removed', $removed ? '1' : '0', $this->generator_search_page_url( $request ) )
		);
	}

	/**
	 * Redirect to the search page.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Redirect_Response
	 */
	protected function generator_search_page_url( Request $request ) {
		$res_request = $this->reservation->resolve_res_request();

		if ( $res_request ) {
			$search_url = ( new Url_Generator( $res_request ) )->get_availability_url();
		} else {
			$search_url = abrs_get_page_permalink( 'search' );
		}

		return $search_url;
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
