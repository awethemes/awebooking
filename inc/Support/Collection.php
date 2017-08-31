<?php
namespace AweBooking\Support;

use Illuminate\Support\Collection as Base_Collection;

class Collection extends Base_Collection {
	/**
	 * Alias of `toArray` method.
	 *
	 * @return array
	 */
	public function to_array() {
		return $this->toArray();
	}

	/**
	 * Alias of `toJson` method.
	 *
	 * @param  int $options JSON encode options.
	 * @return string
	 */
	public function to_json( $options = 0 ) {
		return $this->toJson( $options );
	}
}
