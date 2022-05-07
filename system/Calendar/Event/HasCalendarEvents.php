<?php

namespace AweBooking\System\Calendar\Event;

/**
 * @property-read \AweBooking\Vendor\Illuminate\Database\Eloquent\Collection|EventEntry[] $events
 */
trait HasCalendarEvents
{
	/**
	 * @return \AweBooking\Vendor\Illuminate\Database\Eloquent\Relations\MorphMany
	 */
	public function events()
	{
		return $this->morphMany(EventEntry::class, 'unit');
	}
}
