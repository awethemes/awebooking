<?php
namespace AweBooking\Admin\Controllers;

use Awethemes\Http\Request;
use AweBooking\Admin\Calendar\Booking_Scheduler;

class Calendar_Controller extends Controller {
	/**
	 * Show the booking scheduler.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response
	 */
	public function index( Request $request ) {
		$scheduler = new Booking_Scheduler;

		$scheduler->prepare( $request );

		return $this->response( 'calendar/index.php', compact( 'scheduler' ) );
	}

	/**
	 * Show room_type rate.
	 *
	 * @param \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response
	 */
	public function update( Request $request ) {
		check_admin_referer( 'awebooking_update_state', '_wpnonce' );

		// Get the sanitized values.
		// $sanitized = ( new Room_Price_Form )->handle( $request );

		if ( $request->filled( 'calendar', 'end_date', 'start_date' ) ) {
			$updated = abrs_block_room([
				'room'       => $request->get( 'calendar' ),
				'start_date' => $request->get( 'start_date' ),
				'end_date'   => $request->get( 'end_date' ),
				'only_days'  => $request->get( 'days' ),
			]);

			if ( $updated && ! is_wp_error( $updated ) ) {
				abrs_admin_notices( esc_html__( 'Update state successfully', 'awebooking' ), 'success' )->dialog();
			}
		}

		return $this->redirect()->back( abrs_admin_route( '/calendar' ) );
	}
}
