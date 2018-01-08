<?php
namespace AweBooking\ICalendar\Reader\Adapter;

class Contents_Adapter implements Adapter_Interface {
	/**
	 * Get the data from input.
	 *
	 * @param  string $input The input string.
	 * @return string
	 */
	public function get( $input ) {
		return (string) $input;
	}
}
