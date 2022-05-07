<?php

use AweBooking\System\Calendar\EventTypeInterface;
use AweBooking\Vendor\Illuminate\Support\Collection;
use AweBooking\Vendor\Roomify\Bat\Event\EventInterface;

class AvailabilityTool
{

	/**
	 * @var Collection|EventInterface[]
	 */
	protected $events;

	/**
	 * @var EventTypeInterface
	 */
	protected $eventType;

	/**
	 * @param array|EventInterface[] $events
	 * @param EventTypeInterface $eventType
	 */
	public function __construct(array $events, EventTypeInterface $eventType)
	{
		$this->eventType = $eventType;

		$this->events = Collection::make($events)->map(function (EventInterface $event) {
			return [$event, $this->eventType->getEventState($event->getValue())];
		});
	}

	public function getEvents(): Collection
	{
		return $this->events;
	}

	public function isFullAvailable(): bool
	{
		if ($this->getEvents()->count() !== 1) {
			return false;
		}

		[$event, $state] = $this->events->first();

		return $state && !$state->isBlocking();
	}

	public function isSomeAvailable(): bool
	{
		return $this->rejectBlockingStates()->count() > 1;
	}

	public function onlyBlockingStates(): Collection
	{
		return $this->getEvents()->filter(function ($eventDetail) {
			[$event, $state] = $eventDetail;

			return $state && $state->isBlocking();
		})->values();
	}

	public function rejectBlockingStates(): Collection
	{
		return $this->getEvents()->reject(function ($eventDetail) {
			[$event, $state] = $eventDetail;

			return $state && $state->isBlocking();
		})->values();
	}
}
