<?php
namespace AweBooking\Model\Pricing;

interface Rate_Plan {
	/**
	 * Get the ID.
	 *
	 * @return int
	 */
	public function get_id();

	/**
	 * Get the priority.
	 *
	 * @return int
	 */
	public function get_priority();

	/**
	 * Get the name.
	 *
	 * @return string
	 */
	public function get_name();

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
}
