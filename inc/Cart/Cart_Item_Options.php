<?php
namespace AweBooking\Cart;

use AweBooking\Support\Collection;

class Cart_Item_Options extends Collection {
	/**
	 * Get the option by the given key.
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function __get( $key ) {
		return $this->get( $key );
	}
}
