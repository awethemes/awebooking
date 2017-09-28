<?php
namespace AweBooking\Booking;

use Carbon\Carbon;
use AweBooking\Factory;
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

	public static function from_array( array $request ) {
		$period = new Period( $request['check_in'], $request['check_out'], true );

		return new static( $period, $request );
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
		return ! is_null( $this->get_request( $request ) );
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
		// TODO: Improve this!
		if ( 'extra_services' === $request ) {
			if ( empty( $this->requests['room-type'] ) || empty( $this->requests['extra_services'] ) ) {
				return [];
			}

			$room_type = Factory::get_room_type( $this->requests['room-type'] );
			$allowed_services = [];

			if ( ! $room_type || ! $room_type->exists() ) {
				return [];
			}

			// Validate services.
			foreach ( $this->requests['extra_services'] as $service_id ) {
				if ( ! in_array( $service_id, $room_type['service_ids'] ) ) {
					continue;
				}

				$allowed_services[] = $service_id;
			}

			return $allowed_services;
		}

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

	public function to_array() {
		$request = $this->get_requests();

		$request['check_in']  = $this->get_check_in()->toDateString();
		$request['check_out'] = $this->get_check_out()->toDateString();

		return $request;
	}

	/**
	 * Gets formatted guest number HTML.
	 *
	 * @param  boolean $echo Echo or return output.
	 * @return string|void
	 */
	public function get_fomatted_guest_number( $echo = true ) {
		$html = '';

		$html .= sprintf(
			'<span class="">%1$d %2$s</span>',
			$this->get_adults(),
			_n( 'adult', 'adults', $this->get_adults(), 'awebooking' )
		);

		if ( $this->get_children() ) {
			$html .= sprintf(
				' &amp; <span class="">%1$d %2$s</span>',
				$this->get_children(),
				_n( 'child', 'children', $this->get_children(), 'awebooking' )
			);
		}

		if ( $echo ) {
			print $html; // WPCS: XSS OK.
		} else {
			return $html;
		}
	}
}
