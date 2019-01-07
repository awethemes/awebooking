<?php

namespace AweBooking\Calendar\Event;

interface Formatter {
	/**
	 * Format an event object.
	 *
	 * @param  \AweBooking\Calendar\Event\Event $event The event to format.
	 * @return mixed
	 */
	public function format( Event $event );
}
