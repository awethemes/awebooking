<?php

namespace AweBooking\Model\Pricing\Contracts;

use AweBooking\Model\Common\Timespan;

interface Rate_Interval {
	/**
	 * Gets the ID.
	 *
	 * @return int
	 */
	public function get_id();

	/**
	 * Gets the rate ID.
	 *
	 * @return int
	 */
	public function get_rate_id();

	/**
	 * Gets the name.
	 *
	 * @return string
	 */
	public function get_name();

	/**
	 * Gets the amount.
	 *
	 * @return float
	 */
	public function get_rack_rate();

	/**
	 * Gets the effective_date.
	 *
	 * @return string
	 */
	public function get_effective_date();

	/**
	 * Gets the get_expires_date.
	 *
	 * @return string
	 */
	public function get_expires_date();

	/**
	 * Gets the priority.
	 *
	 * @return int
	 */
	public function get_priority();

	/**
	 * Returns the restrictions.
	 *
	 * @return array
	 */
	public function get_restrictions();

	/**
	 * Gets the rate breakdown.
	 *
	 * @param  \AweBooking\Model\Common\Timespan $timespan The timespan.
	 *
	 * @return \AweBooking\Model\Pricing\Breakdown|\WP_Error
	 */
	public function get_breakdown( Timespan $timespan );
}
