<?php
namespace AweBooking\Admin\Controllers;

use Awethemes\Http\Request;
use AweBooking\Model\Room_Type;
use AweBooking\Model\Common\Timespan;
use AweBooking\Reservation\Room_Stay;
use AweBooking\Reservation\Reservation;
use AweBooking\Reservation\Request as Reservation_Request;
use AweBooking\Admin\List_Tables\Availability_List_Table;

use AweBooking\Reservation\Pricing\Selector;

class Reservation_Controller extends Controller {
	/**
	 * Create the new reservation.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response
	 */
	public function index( Request $request ) {
		switch ( $request->get( 'step' ) ) {
			case 'search':
				return $this->step_search( $request );

			case 'guest':
				return $this->step_guest( $request );

			case 'complete':
				return $this->step_complete( $request );

			default:
				return $this->response_view( 'reservation/create.php' );
		}
	}

	/**
	 * Perform the searching rooms.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response
	 */
	protected function step_search( Request $request ) {
		if ( ! $request->filled( 'source', 'check_in', 'check_out' ) ) {
			$this->notices( 'warning', esc_html__( 'The reservation requests is invalid.', 'awebooking' ) );

			return $this->redirect()->admin_route( 'reservation' );
		}

		// Create new reservation request for the seaching.
		$timespan = new Timespan( $request->get( 'check_in' ), $request->get( 'check_out' ) );
		$res_request = new Reservation_Request( $timespan );

		$constraints = [];

		// Perform the search rooms.
		$results = $res_request->search( $constraints )
			->only_available_items();

		// Create the "Availability_List_Table" for the display.
		$availability_table = new Availability_List_Table( $res_request );
		$availability_table->items = $results;

		return $this->response_view( 'reservation/step-search.php', compact(
			'res_request', 'availability_table', 'reservation'
		));
	}

	/**
	 * Handler add new roomstay.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response
	 */
	public function update( Request $request ) {
		$request->verify_nonce( '_wpnonce', 'add_roomstay' );

		$selected = array_keys( $request->input( 'submit' ) );
		$selected = isset( $selected[0] ) ? absint( $selected[0] ) : null;

		$rate_plan = ( new Selector )->rate_plan( $default );

		$room_type = new Room_Type( $selected );

		$room_stay = new Room_Stay( $room_type, $rate_plan, $request->get_timespan(), $request->get_guest_counts() );
		dd( $room_stay );
	}

	/**
	 * Resovle the reservation request from session.
	 *
	 * @return \AweBooking\Reservation\Reservation|null
	 */
	protected function resovle_reservation() {
		$session = awebooking()->make( 'reservation_admin_session' );
		return;
	}
}
