<?php

namespace AweBooking\System\Calendar;

use AweBooking\System\Calendar\Store\DatabaseStore;
use AweBooking\System\Calendar\Store\StoreFactoryInterface;
use AweBooking\Vendor\Roomify\Bat\Calendar\Calendar;
use AweBooking\Vendor\Roomify\Bat\Store\StoreInterface;
use AweBooking\Vendor\Roomify\Bat\Unit\Unit;

class CalendarFactory
{
	/**
	 * @var EventTypeInterface
	 */
	private $eventType;

	/**
	 * @var StoreFactoryInterface
	 */
	private $storeFactory;

	public function __construct(
		EventTypeInterface $eventType,
		StoreFactoryInterface $storeFactory
	) {
		$this->eventType = $eventType;
		$this->storeFactory = $storeFactory;
	}

	public function getEventType(): EventTypeInterface
	{
		return $this->eventType;
	}

	public function createEventStore(): StoreInterface
	{
		return $this->storeFactory->create(
			$this->eventType->getCode(),
			DatabaseStore::STORE_EVENT
		);
	}

	public function createStateStore(): StoreInterface
	{
		return $this->storeFactory->create(
			$this->eventType->getCode(),
			DatabaseStore::STORE_STATE
		);
	}

	public function createStateCalendar(Unit ...$units): Calendar
	{
		return new Calendar(
			$units,
			$this->createEventStore(),
			$this->eventType->getDefaultState()
		);
	}

	public function createEventCalendar(Unit ...$units): Calendar
	{
		return new Calendar(
			$units,
			$this->createEventStore(),
			0 // Default state for event calendar always 0.
		);
	}
}
