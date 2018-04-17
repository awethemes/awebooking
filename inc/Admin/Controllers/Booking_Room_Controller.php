<?php
namespace AweBooking\Admin\Controllers;

use WP_Error;
use AweBooking\Assert;
use AweBooking\Model\Factory;
use \AweBooking\Model\Common\Guest_Counts;
use AweBooking\Model\Rate;
use AweBooking\Model\Booking;
use AweBooking\Model\Booking_Room_Item;
use AweBooking\Reservation\Creator;
use AweBooking\Reservation\Reservation;
use AweBooking\Reservation\Searcher\Checker;
use AweBooking\Reservation\Searcher\Constraints\Rooms_In_Booking_Constraint;
use AweBooking\Admin\Forms\Search_Reservation_Form;
use AweBooking\Admin\List_Tables\Availability_List_Table;
use AweBooking\Admin\Forms\Edit_Room_Item_Form;
use AweBooking\Support\Utils as U;
use Awethemes\Http\Request;
use Illuminate\Support\Arr;

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

		/*if ( ! $booking->is_editable() ) {
			return new WP_Error( 404, esc_html__( 'This booking is no longer editable.', 'awebooking' ) );
		}*/

		// Create the form controls.
		$controls = new Search_Reservation_Form;

		if ( $request->filled( 'check-in', 'check-out' ) ) {
			// Set the date value if requested.
			$controls['date']->set_value( array_values( $request->only( 'check-in', 'check-out' ) ) );

			// Perform search the reservation.
			$results = abrs_reservation_request([
				'check_in'  => $request->get( 'check-in' ),
				'check_out' => $request->get( 'check-out' ),
			]);

			if ( is_wp_error( $results ) ) {
				return $results;
			}

			$results = $results->search();
		}

		return $this->response( 'booking/add-room.php', compact(
			'request', 'booking', 'controls', 'results'
		));
	}

	/**
	 * Perform search items.
	 *
	 * @param  \AweBooking\Reservation\Reservation $reservation The reservation instance.
	 *  @param \AweBooking\Model\Booking           $booking     The booking reference.
	 * @return \AweBooking\Reservation\Searcher\Results
	 */
	protected function perform_search_items( Reservation $reservation, Booking $booking ) {
		$constraints = apply_filters( 'awebooking/add_room_reservation/constraints', [
			new Rooms_In_Booking_Constraint( $booking ),
		]);

		$results = $reservation->search( $constraints )
			->only_available_items();

		return apply_filters( 'awebooking/add_room_reservation/search_results', $results, $reservation, $booking );
	}
}
