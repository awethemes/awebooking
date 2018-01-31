<?php
namespace AweBooking\Cart;

use AweBooking\Support\Collection;

class Options extends Collection {
	/**
	 * Get the option by the given key.
	 *
	 * @param  string $key Getter key.
	 * @return mixed
	 */
	public function __get( $key ) {
		return $this->get( $key );
	}
}
