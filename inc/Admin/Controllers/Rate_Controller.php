<?php
namespace AweBooking\Admin\Controllers;

use AweBooking\Model\Rate;
use AweBooking\Model\Stay;
use AweBooking\Model\Room_Type;
use AweBooking\Concierge\Pricing\Pricing;
use AweBooking\Admin\Forms\Set_Price_Form;
use AweBooking\Admin\Calendar\Pricing_Calendar;
use Awethemes\Http\Request;

class Rate_Controller extends Admin_Controller {
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

		$set_price_form = new Set_Price_Form( $room_type );

		/* translators: %s Room type name */
		$page_title = sprintf( esc_html__( '%s Pricing', 'awebooking' ), esc_html( $room_type->get_title() ) );

		return $this->response_view( 'rates/show.php', compact(
			'page_title', 'room_type', 'scheduler', 'set_price_form'
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
		$request->validate([
			'set_amount'        => 'required|numeric',
			'set_amount_period' => 'date_period',
		]);

		$stay = new Stay( $request['set_amount_period'][0], $request['set_amount_period'][1] );

		// Set the pricing.
		$updated = ( new Pricing( $rate, $stay ) )
			->set_amount( $request['set_amount'] );

		awebooking( 'admin_notices' )->success( esc_html__( 'Update price successfully!', 'awebooking' ) );

		return $this->redirect()->back();
	}
}
