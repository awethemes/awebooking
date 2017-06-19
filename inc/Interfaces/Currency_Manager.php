<?php

namespace AweBooking\Interfaces;

interface Currency_Manager {
	/**
	 * Gets all currencies.
	 *
	 * @return array
	 */
	public function get_currencies();

	/**
	 * Gets currency by code.
	 *
	 * @param  string $code Currency code. If null passed,
	 *                      default currency will be return.
	 * @return array
	 */
	public function get_currency( $code = null );

	/**
	 * Gets current currency code.
	 *
	 * @return array
	 */
	public function get_current_currency();
}
