<?php
namespace AweBooking\Frontend\Controllers;

use Awethemes\Http\Request;
use AweBooking\Reservation\Reservation;
use AweBooking\Reservation\Room_Stay;
use AweBooking\Model\Pricing\Base_Rate;
use AweBooking\Model\Pricing\Standard_Plan;

class Reservation_Controller {
	/**
	 * Handle book a room from request.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response
	 */
	public function book( Request $request ) {
		$res_request = abrs_reservation_request([
			'check_in'   => $request->get( 'check_in' ),
			'check_out'  => $request->get( 'check_out' ),
			'adults'     => $request->get( 'adults' ),
			'children'   => $request->get( 'children' ),
			'infants'    => $request->get( 'infants' ),
		]);

		$reservation = new Reservation( 'website' );

		$reservation->set_currency( abrs_current_currency() );
		$reservation->set_current_request( $res_request );

		if ( $request->has( 'book_room' ) ) {
			$room = abrs_get_room( $request->get( 'book_room' ) );
			$room_type = abrs_get_room_type( $room['room_type'] );

			$room_rate = new Base_Rate( $room_type );
			$rate_plan = new Standard_Plan( $room_type );

			$room_stay = new Room_Stay( $room_type, $rate_plan, $res_request->get_timespan(), $res_request->get_guest_counts() );
			$room_stay->apply( $room_rate );
			$room_stay->assign( $room );

			dd( $room_stay->get_price() );
		}

		dd( $request->all() );
	}
}
