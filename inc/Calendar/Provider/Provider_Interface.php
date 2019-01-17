<?php

namespace AweBooking\Calendar\Provider;

use AweBooking\Support\Carbonate;

interface Provider_Interface {
	/**
	 * Return events that matches to $start_date && $end_date.
	 *
	 * Note: The $end_date date should be exclude.
	 *
	 * @param  Carbonate $start_date The start date.
	 * @param  Carbonate $end_date   The end date.
	 * @param  array     $options    Optional, extra options.
	 * @return array     \AweBooking\Calendar\Event\Event_Interface[]
	 */
	public function get_events( Carbonate $start_date, Carbonate $end_date, array $options = [] );
}
