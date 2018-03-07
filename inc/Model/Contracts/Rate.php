<?php
namespace AweBooking\Model\Contracts;

use AweBooking\Ruler\Rule;

interface Rate {
	/**
	 * Get the ID.
	 *
	 * @return int
	 */
	public function get_id();

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

	/**
	 * Get the priority.
	 *
	 * @return int
	 */
	public function get_priority();

	/**
	 * Get the rule restrictions..
	 *
	 * @return \AweBooking\Ruler\Rule
	 */
	public function get_restrictions();

	/**
	 * Set the rule restrictions.
	 *
	 * @param \AweBooking\Ruler\Rule $restrictions The rule.
	 */
	public function set_restrictions( Rule $restrictions );
}
