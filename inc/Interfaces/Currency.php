<?php
namespace AweBooking\Interfaces;

interface Currency {
	/* Position constants */
	const POS_LEFT = 'left';
	const POS_RIGHT = 'right';
	const POS_LEFT_SPACE = 'left_space';
	const POS_RIGHT_SPACE = 'right_space';

	/**
	 * Gets the alphabetic currency code.
	 *
	 * @return string
	 */
	public function get_code();

	/**
	 * Gets the currency name.
	 *
	 * @return string
	 */
	public function get_name();

	/**
	 * Gets the currency symbol.
	 *
	 * @return string
	 */
	public function get_symbol();
}
