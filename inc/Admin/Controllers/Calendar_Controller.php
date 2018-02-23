<?php
namespace AweBooking\Admin\Controllers;

use Awethemes\Http\Request;
use AweBooking\Model\Room_Type;
use AweBooking\Admin\Calendar\Availability_Calendar;

class Calendar_Controller extends Controller {
	/**
	 * Show all room-types.
	 *
	 * @return \Awethemes\Http\Response
	 */
	public function index() {
		return $this->response_view( 'calendar/index.php' );
	}

	/**
	 * Show the room-type pricing calendar.
	 *
	 * @param \Awethemes\Http\Request     $request   The current request.
	 * @param \AweBooking\Model\Room_Type $room_type The room_type instance.
	 * @return \Awethemes\Http\Response
	 */
	public function show( Request $request, Room_Type $room_type ) {
		$scheduler = new Availability_Calendar( $room_type );

		return $this->response_view( 'calendar/show.php', compact(
			'room_type', 'scheduler'
		));
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
