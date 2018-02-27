<?php
namespace AweBooking\Admin\Controllers;

use Awethemes\Http\Request;
use AweBooking\Model\Room_Type;
use AweBooking\Admin\Calendar\Main_Calendar;

class Calendar_Controller extends Controller {
	/**
	 * Show all room-types.
	 *
	 * @return \Awethemes\Http\Response
	 */
	public function index( Request $request ) {
		$scheduler = new Main_Calendar;

		return $this->response_view( 'calendar/main.php', compact( 'scheduler' ) );
	}

	/**
	 * Show room_type rate.
	 *
	 * @param \Awethemes\Http\Request $request The current request.
	 * @param \AweBooking\Model\Rate  $rate    The rate instance.
	 * @return \Awethemes\Http\Response
	 */
	public function update( Request $request, Rate $rate ) {
	}
}
