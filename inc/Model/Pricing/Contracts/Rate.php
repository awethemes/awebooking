<?php

namespace AweBooking\Model\Pricing\Contracts;

interface Rate {
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
	 * Gets all services inclusions.
	 *
	 * @return array
	 */
	public function get_services();

	/**
	 * Gets the priority.
	 *
	 * @return int
	 */
	public function get_priority();

	/**
	 * Gets all rate intervals.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function get_rate_intervals();

	/**
	 * Returns whether or not the rate is taxable.
	 *
	 * @return bool
	 */
	public function is_taxable();

	/**
	 * Returns the tax rate ID.
	 *
	 * @return int
	 */
	public function get_tax_rate();

	/**
	 * Determines if prices inclusive of tax or not.
	 *
	 * @return bool
	 */
	public function price_includes_tax();
}
