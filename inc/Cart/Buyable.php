<?php
namespace AweBooking\Cart;

interface Buyable {
	/**
	 * Determines the Buyable item is purchasable.
	 *
	 * @return boolean
	 */
	public function is_purchasable( $options );

	/**
	 * Get the identifier of the Buyable item.
	 *
	 * @return int|string
	 */
	public function get_buyable_identifier( $options );

	/**
	 * Get the price of the Buyable item.
	 *
	 * @return float
	 */
	public function get_buyable_price( $options );
}
