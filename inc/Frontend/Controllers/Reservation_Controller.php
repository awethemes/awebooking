<?php
namespace AweBooking\Frontend\Controllers;

use Awethemes\Http\Request;

class Reservation_Controller {
	/**
	 * Handle book a room from request.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response
	 */
	public function book( Request $request ) {
		dd( $request->all() );
	}
}
