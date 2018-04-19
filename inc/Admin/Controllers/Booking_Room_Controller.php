<?php
namespace AweBooking\Admin\Controllers;

use WP_Error;
use Awethemes\Http\Request;
use AweBooking\Reservation\Reservation;
use AweBooking\Reservation\Search\Constraints\Rooms_In_Booking_Constraint;

class Booking_Room_Controller extends Controller {
	/**
	 * Handle store the settings.
	 *
	 * @param  \Awethemes\Http\Request   $request The current request.
	 * @param  \AweBooking\Model\Booking $booking The booking reference.
	 * @return \Awethemes\Http\Response
	 */
	public function create( Request $request ) {
		// Get the check the booking reference.
		if ( ! $booking = abrs_get_booking( $request['refer'] ) ) {
			return new WP_Error( 404, esc_html__( 'The booking reference is does not exist.', 'awebooking' ) );
		}

		if ( ! $booking->is_editable() ) {
			return new WP_Error( 404, esc_html__( 'This booking is no longer editable.', 'awebooking' ) );
		}

		if ( $request->filled( 'check-in', 'check-out' ) ) {
			$results = abrs_reservation_request([
				'check_in'  => $request->get( 'check-in' ),
				'check_out' => $request->get( 'check-out' ),
			]);

			if ( is_wp_error( $results ) ) {
				return $results;
			}

			$results = $results->search()
				->only_available_items();
		}

		return $this->response( 'booking/add-room.php', compact(
			'request', 'booking', 'results'
		));
	}

	/**
	 * Perform search items.
	 *
	 * @param  \AweBooking\Reservation\Reservation $reservation The reservation instance.
	 *  @param \AweBooking\Model\Booking           $booking     The booking reference.
	 * @return \AweBooking\Reservation\Search\Results
	 */
	protected function perform_search_items( Reservation $reservation, Booking $booking ) {
		$constraints = apply_filters( 'awebooking/add_room_reservation/constraints', [
			new Rooms_In_Booking_Constraint( $booking ),
		]);

		$results = $reservation->search( $constraints )
			->only_available_items();

		return apply_filters( 'awebooking/add_room_reservation/search_results', $results, $reservation, $booking );
	}


	/**
	 * Handle store new booking payment.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response
	 */
	public function store( Request $request ) {
		check_admin_referer( 'create_booking_payment', '_wpnonce' );

		if ( ! $request->filled( '_refer' ) || ! $booking = abrs_get_booking( $request['_refer'] ) ) {
			return $this->whoops();
		}
	}
}
