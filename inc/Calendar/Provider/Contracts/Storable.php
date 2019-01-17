<?php

namespace AweBooking\Calendar\Provider\Contracts;

use AweBooking\Calendar\Event\Event_Interface;

interface Storable {
	/**
	 * Given an event, save it and return true if successful.
	 *
	 * @param  Event_Interface $event The event to store.
	 * @return bool
	 */
	public function store_event( Event_Interface $event );
}
