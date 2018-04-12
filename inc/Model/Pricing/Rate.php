<?php
namespace AweBooking\Model\Pricing;

interface Rate {
	/**
	 * Get the priority.
	 *
	 * @return int
	 */
	public function get_priority();

	/**
	 * Get the parent ID.
	 *
	 * @return int
	 */
	public function get_parent_id();

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
	 * Get the amount.
	 *
	 * @return \AweBooking\Support\Decimal
	 */
	public function get_rack_rate();

	/**
	 * Get the effective_date.
	 *
	 * @return string
	 */
	public function get_effective_date();

	/**
	 * Get the expire_date.
	 *
	 * @return string
	 */
	public function get_expire_date();
}
