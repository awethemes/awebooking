<?php
namespace AweBooking\Model\Pricing;

interface Rate_Plan {
	/**
	 * Gets the ID.
	 *
	 * @return int
	 */
	public function get_id();

	/**
	 * Gets the rates.
	 *
	 * @return array \AweBooking\Model\Pricing[]
	 */
	public function get_rates();

	/**
	 * Gets the name.
	 *
	 * @return string
	 */
	public function get_name();

	/**
	 * Gets the policies.
	 *
	 * @return string
	 */
	public function get_policies();

	/**
	 * Gets the inclusions.
	 *
	 * @return string
	 */
	public function get_inclusions();

	/**
	 * Gets the priority.
	 *
	 * @return int
	 */
	public function get_priority();
}
