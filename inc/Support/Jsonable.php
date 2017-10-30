<?php
namespace AweBooking\Support;

use Illuminate\Contracts\Support\Jsonable as Illuminate_Jsonable;

interface Jsonable extends Illuminate_Jsonable {
	/**
	 * Convert the object to its JSON representation.
	 *
	 * Alias of $this->toJson() method.
	 *
	 * @param  int $options Optional, json_encode options.
	 * @return string
	 */
	public function to_json( $options = 0 );
}
