<?php
namespace AweBooking\Booking;

use Carbon\Carbon;
use AweBooking\AweBooking;
use AweBooking\Support\Period;

class Request {
	/**
	 * Date Period instance.
	 *
	 * @var Period
	 */
	protected $period;

	/**
	 * Booking requests.
	 *
	 * @var array
	 */
	protected $requests;

	public static function instance() {
		$wp_session = awebooking()->make( 'session' );

		if ( empty( $wp_session['awebooking_request'] ) ) {
			throw new \RuntimeException( 'Missing booking data' );
		}

		$requests = $wp_session['awebooking_request']->toArray();
		$period = new Period( $requests['check_in'], $requests['check_out'], true );

		return new static( $period, $requests );
	}

	/**
	 * Booking request constructor.
	 *
	 * @param Period $period   Period days.
	 * @param array       $requests An array of booking requests.
	 */
	public function __construct( Period $period, array $requests = [] ) {
		$this->period = $period;
		$this->requests = $requests;
	}

	public function store() {
		$wp_session = awebooking()->make( 'session' );

		$store_request              = $this->get_requests();
		$store_request['check_in']  = $this->get_check_in()->toDateString();
		$store_request['check_out'] = $this->get_check_out()->toDateString();

		$wp_session['awebooking_request'] = $store_request;
		wp_session_commit();
	}

	/**
	 * Return the check-in day.
	 *
	 * @return \Carbon\Carbon
	 */
	public function get_check_in() {
		return $this->period->get_start_date();
	}

	/**
	 * Return the check-out day.
	 *
	 * @return \Carbon\Carbon
	 */
	public function get_check_out() {
		return $this->period->get_end_date();
	}

	/**
	 * Get nights.
	 *
	 * @return int
	 */
	public function get_nights() {
		return $this->period->nights();
	}

	/**
	 * Get the date period instance.
	 *
	 * @return Period
	 */
	public function get_period() {
		return $this->period;
	}

	/**
	 * Get number of adults.
	 *
	 * @return int
	 */
	public function get_adults() {
		return $this->has_request( 'adults' ) ? $this->get_request( 'adults' ) : -1;
	}

	/**
	 * Get number of children.
	 *
	 * @return int
	 */
	public function get_children() {
		return $this->has_request( 'children' ) ? $this->get_request( 'children' ) : -1;
	}

	/**
	 * Get people.
	 *
	 * @return int
	 */
	public function get_people() {
		$people = $this->get_adults() + $this->get_children();

		return $people > 0 ? $people : -1;
	}

	/**
	 * Get request services.
	 *
	 * @return array
	 */
	public function get_services() {
		if ( ! $this->has_request( 'extra_services' ) ) {
			return [];
		}

		$services = [];
		foreach ( (array) $this->get_request( 'extra_services' ) as $service_id => $quantity ) {
			if ( is_int( $service_id ) ) {
				$service_id = $quantity;
				$quantity = 1;
			}

			$services[ $service_id ] = $quantity ? absint( $quantity ) : 1;
		}

		return $services;
	}

	/**
	 * If has a booking request.
	 *
	 * @param  string $request Booking request.
	 * @return bool
	 */
	public function has_request( $request ) {
		return isset( $this->requests[ $request ] );
	}

	/**
	 * Set a booking request.
	 *
	 * @param  string $key   Booking request key.
	 * @param  string $value Booking request value.
	 * @return $this
	 */
	public function set_request( $key, $value ) {
		$this->requests[ $key ] = $value;

		return $this;
	}

	/**
	 * Get booking request.
	 *
	 * @param  string $request Booking request.
	 * @return mixed
	 */
	public function get_request( $request ) {
		return isset( $this->requests[ $request ] ) ?
			$this->requests[ $request ] :
			null;
	}

	/**
	 * Get booking requests.
	 *
	 * @return mixed
	 */
	public function get_requests() {
		return $this->requests;
	}

	/**
	 * Remove a booking request.
	 *
	 * @param  string $key Booking request key.
	 * @return $this
	 */
	public function remove_request( $key ) {
		unset( $this->requests[ $key ] );

		return $this;
	}

	/**
	 * Return valid states for the Calendar.
	 *
	 * @return array
	 */
	public function valid_states() {
		return [ AweBooking::STATE_AVAILABLE ];
	}

	/**
	 * Return array of constraints for the Calendar.
	 *
	 * @return array
	 */
	public function constraints() {
		return [];
	}
}
