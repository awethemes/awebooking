<?php
namespace AweBooking\Admin\Controllers;

use AweBooking\Model\Stay;
use AweBooking\Model\Room_Type;
use AweBooking\Support\Decimal;
use AweBooking\Calendar\Factory;
use AweBooking\Calendar\Event\Pricing_Event;
use AweBooking\Admin\Calendar\Rate_Calendar;
use Awethemes\Http\Request;

class Rate_Controller extends Controller {
	/**
	 * Show all room-types.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response
	 */
	public function index( Request $request ) {
		$scheduler = new Rate_Calendar;

		return $this->response_view( 'rates/index.php', compact( 'scheduler' ) );
	}

	/**
	 * Show room_type rate.
	 *
	 * @param \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response
	 */
	public function update( Request $request ) {
		$request->verify_nonce( '_wpnonce', 'awebooking_update_price' );

		// The fallback url to redirect back.
		$fallback_url = awebooking( 'url' )->admin_route( 'rates' );

		try {
			$stay = new Stay( $request->get( 'start_date' ), $request->get( 'end_date' ) );
		} catch ( \Exception $e ) {
			return $this->redirect()->back( $fallback_url );
		}

		// The calendar (resource) will be apply.
		$apply_calendar = absint( $request->get( 'calendar' ) );
		// TODO: Validate resource.

		if ( 'reset_price' === $request->get( 'action' ) ) {
			$amount = 0;
		} else {
			$amount = $request->get( 'amount', 0 );
		}

		$this->perform_update_amount( $stay, $apply_calendar, $amount );

		return $this->redirect()->back( $fallback_url );
	}

	/**
	 * Perform update amount for a given resource.
	 *
	 * @param  \AweBooking\Model\Stay $stay     The stay period to apply.
	 * @param  int                    $resource The resource.
	 * @param  int|float              $amount   The amount.
	 * @return bool
	 */
	protected function perform_update_amount( Stay $stay, $resource, $amount ) {
		$calendar = ( new Factory )->create_pricing_calendar( $resource );

		// Because we handle update date range by "daily", but the Calendar
		// works with "nightly", so we need adjust the $end_date +1day.
		$start_date = $stay->get_check_in();
		$end_date = $stay->get_check_out()->addDay();

		// Create the pricing event.
		$event = new Pricing_Event( $calendar->get_resource(), $start_date, $end_date, Decimal::create( $amount ) );

		return $calendar->store( $event );
	}
}
