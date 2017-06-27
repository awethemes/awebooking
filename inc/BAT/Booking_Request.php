<?php
namespace AweBooking\BAT;

use Carbon\Carbon;
use AweBooking\Room_State;
use AweBooking\Support\Date_Period;
use AweBooking\Interfaces\Booking_Request as Booking_Request_Interface;

class Booking_Request implements Booking_Request_Interface {
	/**
	 * Date Period instance.
	 *
	 * @var Date_Period
	 */
	protected $period;

	/**
	 * Booking requests.
	 *
	 * @var array
	 */
	protected $requests;

	/**
	 * Booking request constructor.
	 *
	 * @param Date_Period $period   Period days.
	 * @param array       $requests An array of booking requests.
	 */
	public function __construct( Date_Period $period, array $requests = [] ) {
		$this->period = $period;
		$this->requests = $requests;
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
	 * @return Date_Period
	 */
	public function get_period() {
		return $this->period;
	}

	/**
	 * Get number of adults.
	 *
	 * @return integer
	 */
	public function get_adults() {
		return $this->has_request( 'adults' ) ? $this->get_request( 'adults' ) : -1;
	}

	/**
	 * Get number of children.
	 *
	 * @return integer
	 */
	public function get_children() {
		return $this->has_request( 'children' ) ? $this->get_request( 'children' ) : -1;
	}

	/**
	 * Get people.
	 *
	 * @return integer
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
	public function get_request_services() {
		if ( ! $this->has_request( 'extra_services' ) ) {
			return [];
		}

		$services = [];
		foreach ( (array) $this->get_request( 'extra_services' ) as $slug => $number ) {
			if ( is_int( $slug ) ) {
				$slug = $number;
				$number = 1;
			}

			$services[ $slug ] = $number ? absint( $number ) : 1;
		}

		return $services;
	}

	/**
	 * If has a booking request.
	 *
	 * @param  string $request Booking request.
	 * @return boolean
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
	 * Return valid states for the Calendar.
	 *
	 * @return array
	 */
	public function valid_states() {
		return [ Room_State::AVAILABLE ];
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
