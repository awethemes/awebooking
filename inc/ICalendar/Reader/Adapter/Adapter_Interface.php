<?php
namespace AweBooking\ICalendar\Reader\Adapter;

interface Adapter_Interface {
	/**
	 * Get the data from input.
	 *
	 * @param  mixed $input Input file name or url.
	 * @return mixed
	 */
	public function get( $input );
}
