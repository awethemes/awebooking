<?php
namespace AweBooking\Model\Pricing;

interface Rate {
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
	 * Get the rate plan ID.
	 *
	 * @return int
	 */
	public function get_parent_id();

	/**
	 * Get the name.
	 *
	 * @return string
	 */
	public function get_name();

	/**
	 * Get the amount.
	 *
	 * @return \AweBooking\Support\Decimal
	 */
	public function get_amount();
}
