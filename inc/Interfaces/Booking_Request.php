<?php
namespace AweBooking\Interfaces;

interface Booking_Request {
	/**
	 * Return the check-in date.
	 *
	 * @return \Carbon\Carbon
	 */
	public function get_check_in();

	/**
	 * Return the check-out date.
	 *
	 * @return \Carbon\Carbon
	 */
	public function get_check_out();

	/**
	 * Get nights.
	 *
	 * @return int
	 */
	public function get_nights();

	/**
	 * Get booking request.
	 *
	 * @param  string $request Booking request.
	 * @return mixed
	 */
	public function get_request( $request );

	/**
	 * Return valid states for the Calendar.
	 *
	 * @return array
	 */
	// public function get_states();

	/**
	 * Return array of constraints for the Calendar.
	 *
	 * @return array
	 */
	// public function get_constraints();
}
