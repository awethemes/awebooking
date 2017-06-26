<?php
namespace AweBooking\Interfaces;

interface Price {
	/**
	 * Returns the price amount.
	 *
	 * @return string
	 */
	public function get_amount();

	/**
	 * Set price amount.
	 *
	 * @param float $amount Price amount.
	 */
	public function set_amount( $amount );
}
