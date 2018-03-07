<?php
namespace AweBooking\Model\Contracts;

interface Rate_Plan {
	/**
	 * Get the ID.
	 *
	 * @return int
	 */
	public function get_id();

	/**
	 * Get the name.
	 *
	 * @return string
	 */
	public function get_name();

	/**
	 * Get the priority.
	 *
	 * @return int
	 */
	public function get_priority();

	/**
	 * Get the policies.
	 *
	 * @return string
	 */
	public function get_policies();

	/**
	 * Get the inclusions.
	 *
	 * @return string
	 */
	public function get_inclusions();

	/**
	 * Get all rates.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function get_rates();
}
