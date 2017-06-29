<?php
namespace AweBooking\BAT;

use Carbon\Carbon;
use AweBooking\Room_State;
use AweBooking\Support\Utils;
use AweBooking\Support\Date_Period;
use AweBooking\Interfaces\Booking_Request as Booking_Request_Interface;

class Session_Booking_Request extends Booking_Request {

	/**
	 * Set request instance to cookie.
	 *
	 * @param Booking_Request_Interface $request Request instance.
	 */
	public static function set_instance( Booking_Request_Interface $request ) {
		$request_args = $request->get_requests();

		$request_args['check_in'] = $request->get_check_in()->format( 'Y-m-d' );
		$request_args['check_out'] = $request->get_check_out()->format( 'Y-m-d' );

		Utils::setcookie( 'awebooking-request', maybe_serialize( $request_args ) );
		return $request;
	}

	/**
	 * Booking request constructor.
	 *
	 * @throws \RuntimeException
	 */
	public function __construct() {
		if ( ! isset( $_COOKIE['awebooking-request'] ) ) {
			throw new \RuntimeException( 'Missing booking data' );
		}

		$requests = maybe_unserialize( wp_unslash( $_COOKIE['awebooking-request'] ) );

		$period = new Date_Period( $requests['check_in'], $requests['check_out'] );
		parent::__construct( $period, $requests );
	}
}
