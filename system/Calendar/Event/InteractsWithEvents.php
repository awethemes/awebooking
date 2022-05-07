<?php

namespace AweBooking\System\Calendar;

use AweBooking\System\Calendar\Entry\EventEntryInterface;
use AweBooking\System\Database\Model;
use AweBooking\Vendor\Illuminate\Database\Eloquent\Builder;
use AweBooking\Vendor\Roomify\Bat\Event\Event;
use AweBooking\Vendor\Roomify\Bat\Unit\Unit;
use DateTime;
use DateTimeInterface;
use InvalidArgumentException;

trait InteractsWithEvents
{
	abstract public function getCalendarFactory(): CalendarFactory;

	public function storeEvent(
		$unit,
		int $stateOrValue,
		DateTimeInterface $startDate,
		DateTimeInterface $endDate,
		$reference = null
	) {
		$entryEvent = $this->newEventInstance(
			$unit,
			$stateOrValue,
			$startDate,
			$endDate,
			$reference
		);

		if ($entryEvent instanceof Model) {
			$entryEvent->saveOrFail();
		}

		return false;
	}

	private function newEventInstance(
		$unit,
		int $stateOrValue,
		DateTimeInterface $startDate,
		DateTimeInterface $endDate,
		$reference = null
	) {
		if (!is_a($unit, $this->getUnitEntryClass())) {
			throw new InvalidArgumentException(
				sprintf(
					'The unit must be an instance of %s, %s given',
					$this->getUnitEntryClass(),
					get_class($unit)
				)
			);
		}

		$eventType = $this->getCalendarFactory()->getEventType();
		$entryClass = $eventType->getEventEntryClass();

		/** @var EventEntryInterface $entry */
		$entry = new $entryClass();

		// TODO: Validate state.
		// TODO: Validate reference matching with state.

		$entry->setEventType($this);

		$entry->setUnit($unit);
		$entry->setValue($stateOrValue);
		$entry->setStartDate($startDate);
		$entry->setEndDate($endDate);

		if ($reference !== null) {
			$entry->setReference($reference);
		}

		return $entry;
	}

	public function storeBatEvent(EventEntryInterface $entry, bool $isRemove = false)
	{
		if (!is_a($entry, $this->getEventEntryClass())) {
			throw new InvalidArgumentException(
				sprintf(
					'The entry must be an instance of %s, %s given',
					$this->getUnitEntryClass(),
					get_class($entry)
				)
			);
		}

		$entry->setEventType($this);

		if ($entry->getUnit() && !is_a($entry->getUnit(), $this->getUnitEntryClass())) {
			return false;
		}

		if (!$this->createUnitQueryBuilder()->find($entry->getUnitId())) {
			return false;
		}

		$stateUnit = new Unit($entry->getUnitId(), $this->getDefaultState());
		$eventUnit = new Unit($entry->getUnitId(), 0);

		// TODO: Ensure store date-time as UTC timezone.
		$stateEvent = new Event(
			new DateTime($entry->getStartDate()->format('Y-m-d H:i:s')),
			new DateTime($entry->getEndDate()->format('Y-m-d H:i:s')),
			$stateUnit,
			$isRemove ? $stateUnit->getDefaultValue() : $entry->getValue()
		);

		$eventRefEvent = new Event(
			new DateTime($entry->getStartDate()->format('Y-m-d H:i:s')),
			new DateTime($entry->getEndDate()->format('Y-m-d H:i:s')),
			$stateUnit,
			$isRemove ? 0 : $entry->getId()
		);

		$stateCalendar = $this
			->getCalendarFactory()
			->createStateCalendar($stateUnit);

		$eventCalendar = $this
			->getCalendarFactory()
			->createEventCalendar($eventUnit);

		// TODO: Handle errors.
		$stateCalendar->addEvents([$stateEvent], $this->getGranularity());
		$eventCalendar->addEvents([$eventRefEvent], $this->getGranularity());
	}

	protected function createUnitQueryBuilder(): Builder
	{
		$class = $this->getUnitEntryClass();

		return (new $class())->newQuery();
	}

	protected function createEventQueryBuilder(): Builder
	{
		$class = $this->getEventEntryClass();

		return (new $class())->newQuery();
	}
}
