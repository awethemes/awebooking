<?php
namespace AweBooking\Admin\Controllers;

use AweBooking\Model\Rate;
use AweBooking\Model\Stay;
use AweBooking\Model\Room_Type;
use AweBooking\Reservation\Pricing\Rate_Pricing;
use AweBooking\Admin\Forms\Set_Price_Form;
use AweBooking\Admin\Calendar\Pricing_Calendar;
use Awethemes\Http\Request;

class Rate_Controller extends Controller {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->check_capability( 'manage_awebooking' );
	}

	/**
	 * Show all room-types.
	 *
	 * @return \Awethemes\Http\Response
	 */
	public function index() {
		return $this->response_view( 'rates/index.php' );
	}

	/**
	 * Show the room-type pricing calendar.
	 *
	 * @param \Awethemes\Http\Request     $request   The current request.
	 * @param \AweBooking\Model\Room_Type $room_type The room_type instance.
	 * @return \Awethemes\Http\Response
	 */
	public function show( Request $request, Room_Type $room_type ) {
		$scheduler = new Pricing_Calendar( $room_type );

		wp_enqueue_script( 'awebooking-schedule-calendar' );

		return $this->response_view( 'rates/show.php', compact(
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
	public function set_amount( Request $request, Rate $rate ) {
		$request->verify_nonce( '_nonce', 'set_rate_' . $rate->get_id() );

		$stay = new Stay( $request['set_amount_period'][0], $request['set_amount_period'][1] );

		// Set the pricing.
		$updated = ( new Rate_Pricing( $rate, $stay ) )
			->set_amount( $request['set_amount'] );

		awebooking( 'admin_notices' )->success( esc_html__( 'Update price successfully!', 'awebooking' ) );

		return $this->redirect()->back();
	}
}
