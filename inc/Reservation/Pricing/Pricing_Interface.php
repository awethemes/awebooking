<?php
namespace AweBooking\Reservation\Pricing;

use AweBooking\Model\Stay;

interface Pricing_Interface {
	/**
	 * Get the stay.
	 *
	 * @return \AweBooking\Model\Stay
	 */
	public function get_stay();

	/**
	 * Set the stay.
	 *
	 * @param  \AweBooking\Model\Stay $stay The stay.
	 * @return void
	 */
	public function set_stay( Stay $stay );

	/**
	 * Get the total amount.
	 *
	 * @return \AweBooking\Support\Decimal
	 */
	public function get_amount();

	/**
	 * Set the amount.
	 *
	 * @param  float|int $amount The amount.
	 * @return void
	 */
	public function set_amount( $amount );

	/**
	 * Get the breakdown.
	 *
	 * @return \AweBooking\Reservation\Pricing\Breakdown
	 */
	public function get_breakdown();
}
