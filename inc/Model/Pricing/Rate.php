<?php
namespace AweBooking\Model\Pricing;

interface Rate {
	/**
	 * Gets the parent ID.
	 *
	 * @return int
	 */
	public function get_parent_id();

	/**
	 * Gets the ID.
	 *
	 * @return int
	 */
	public function get_id();

	/**
	 * Gets the name.
	 *
	 * @return string
	 */
	public function get_name();

	/**
	 * Gets the amount.
	 *
	 * @return \AweBooking\Support\Decimal
	 */
	public function get_rack_rate();

	/**
	 * Gets the effective_date.
	 *
	 * @return string
	 */
	public function get_effective_date();

	/**
	 * Gets the expire_date.
	 *
	 * @return string
	 */
	public function get_expire_date();

	/**
	 * Gets the priority.
	 *
	 * @return int
	 */
	public function get_priority();

	/**
	 * Gets all restrictions.
	 *
	 * @return array
	 */
	public function get_restrictions();
}
