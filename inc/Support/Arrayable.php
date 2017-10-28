<?php
namespace AweBooking\Support;

use Illuminate\Contracts\Support\Arrayable as Illuminate_Arrayable;

interface Arrayable extends Illuminate_Arrayable {
	/**
	 * Get the instance as an array.
	 *
	 * Alias of $this->toArray() method.
	 *
	 * @return array
	 */
	public function to_array();
}
