<?php
namespace AweBooking\Admin\Controllers;

use AweBooking\Dropdown;
use AweBooking\Model\Room_Type;
use AweBooking\Model\Common\Timespan;
use AweBooking\Calendar\Factory;
use AweBooking\Calendar\Event\Pricing_Event;
use AweBooking\Admin\Calendar\Rate_Calendar;
use AweBooking\Support\Decimal;
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

		// Enqueue the scripts.
		add_action( 'admin_enqueue_scripts', function() {
			wp_enqueue_script( 'awebooking-manager-pricing' );
		});

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
			$timespan = new Timespan( $request->get( 'start_date' ), $request->get( 'end_date' ) );
		} catch ( \Exception $e ) {
			return $this->redirect()->back( $fallback_url );
		}

		// The calendar (resource) will be apply.
		$apply_calendar = absint( $request->get( 'calendar' ) );
		$this->perform_update_amount( $timespan, $apply_calendar, $request->get( 'value' ), $request->get( 'adjust_operator' ) );

		return $this->redirect()->back( $fallback_url );
	}

	/**
	 * Perform update amount for a given resource.
	 *
	 * @param  \AweBooking\Model\Common\Timespan $timespan     The stay period to apply.
	 * @param  int                    $resource The resource.
	 * @param  int|float              $amount   The amount.
	 * @return bool
	 */
	protected function perform_update_amount( Timespan $timespan, $resource, $value, $operator ) {
		$calendar = ( new Factory )->create_pricing_calendar( $resource );

		$timespan->set_end_date( $timespan->get_end_date()->addDay() );
		$events = $calendar->get_events( $timespan->to_period() );

		foreach ( $events as $event ) {
			$current_amount = Decimal::from_raw_value( $event->get_value() );
			if ( $current_amount->is_zero() ) {
			}

			try {
				$set_amount = $this->calculate_set_amount( $current_amount, $value, $operator );
			} catch ( \Exception $e ) {
				$set_amount = $current_amount;
			}

			$event->set_value( $set_amount );
			$event->set_end_date( $event->get_end_date()->addDay() );

			$calendar->store( $event );
		}

		return true;
	}

	/**
	 * [calculate_set_amount description]
	 *
	 * @param  [type] $current_amount [description]
	 * @param  [type] $value          [description]
	 * @param  [type] $operator         [description]
	 * @return [type]
	 */
	protected function calculate_set_amount( $current_amount, $value, $operator ) {
		$value = Decimal::create( $value );

		switch ( $operator ) {
			case 'replace':
				$amount = $value;
				break;

			case 'add':
				$amount = $current_amount->add( $value );
				break;

			case 'subtract':
				$amount = $current_amount->subtract( $value );
				break;

			case 'multiply':
				$amount = $current_amount->multiply( $value );
				break;

			case 'divide':
				$amount = $current_amount->divide( $value );
				break;

			case 'increase':
				$amount = $current_amount->add( $current_amount->to_percentage( $value ) );
				break;

			case 'decrease':
				$amount = $current_amount->discount( $value );
				break;

			default:
				$amount = Decimal::zero();
				break;
		}

		return $amount;
	}
}
