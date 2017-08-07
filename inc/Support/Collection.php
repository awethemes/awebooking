<?php
namespace AweBooking\Support;

use AweBooking\Interfaces\Jsonable;
use AweBooking\Interfaces\Arrayable;
use Illuminate\Support\Collection as Illuminate_Collection;

class Collection extends Illuminate_Collection implements Arrayable, Jsonable {
	/**
	 * Get the collection of items as a plain array.
	 *
	 * @return array
	 */
	public function to_array() {
		return $this->toArray();
	}

	/**
	 * Get the collection of items as JSON.
	 *
	 * @param  int $options JSON encode options.
	 * @return string
	 */
	public function to_json( $options = 0 ) {
		return $this->toJson( $options );
	}
}
