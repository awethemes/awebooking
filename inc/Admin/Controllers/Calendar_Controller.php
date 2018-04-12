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
}
