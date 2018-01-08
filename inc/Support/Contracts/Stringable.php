<?php
namespace AweBooking\Support\Contracts;

interface Stringable {
	/**
	 * Get content as a string.
	 *
	 * @return string
	 */
	public function as_string();

	/**
	 * This method will be ran when casting this object to string.
	 *
	 * This method {@link http://stackoverflow.com/a/2429735/565229 cannot throw an exception},
	 * but can use `trigger_error()`.
	 *
	 * @return string A string representation of this object.
	 */
	public function __toString();
}
