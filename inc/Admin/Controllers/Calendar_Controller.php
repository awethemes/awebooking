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

		if ( $request->filled( 'room', 'end_date', 'start_date' ) ) {
			$action = $request->get( 'action', 'unblock' );

			switch ( $action ) {
				case 'block':
					$updated = abrs_block_room(
						$request->only( 'room', 'start_date', 'end_date', 'only_days' )
					);
					break;

				case 'unblock':
					$updated = abrs_unblock_room(
						$request->only( 'room', 'end_date', 'start_date' )
					);
					break;

				default:
					do_action( 'awebooking/admin_room_action', $action, $request );
					break;
			}

			if ( ! empty( $updated ) && ! is_wp_error( $updated ) ) {
				abrs_admin_notices( esc_html__( 'Update state successfully', 'awebooking' ), 'success' )->dialog();
			}
		}

		return $this->redirect()->back( abrs_admin_route( '/calendar' ) );
	}
}
