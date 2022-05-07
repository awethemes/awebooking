<?php

namespace AweBooking\System\Calendar;

use InvalidArgumentException;

class EventTypeBuilder extends EventType
{
	private $resolved = false;

	public static function daily(string $code, string $type = null): EventTypeBuilder
	{
		return new static($code, $type, EventTypeInterface::GRANULARITY_DAILY);
	}

	public static function hourly(string $code, string $type = null): EventTypeBuilder
	{
		return new static($code, $type, EventTypeInterface::GRANULARITY_HOURLY);
	}

	public function setType(string $type)
	{
		$this->guardResolved();

		$valid = [
			EventTypeInterface::TYPE_FIXED_STATES,
			EventTypeInterface::TYPE_ARBITRARY_STATES,
		];

		if (!in_array($type, $valid, true)) {
			throw new InvalidArgumentException('Event type must be either fixed or arbitrary');
		}

		$this->type = $type;

		return $this;
	}

	public function setGranularity(string $granularity)
	{
		$this->guardResolved();

		$validKeys = [
			EventTypeInterface::GRANULARITY_HOURLY,
			EventTypeInterface::GRANULARITY_DAILY,
		];

		if (!in_array($granularity, $validKeys, true)) {
			throw new InvalidArgumentException(
				'Only EventTypeInterface::DAILY or EventTypeInterface::HOURLY is allowed.'
			);
		}

		$this->granularity = $granularity;

		return $this;
	}

	public function setDefaultState(int $defaultState)
	{
		$this->guardResolved();

		$this->defaultState = $defaultState;

		return $this;
	}

	public function setAllowedStates(array $states)
	{
		$this->guardResolved();

		$this->allowedStates = $states;

		return $this;
	}

	public function setEventEntryClass(string $eventEntryClass)
	{
		$this->guardResolved();

		$this->eventEntryClass = $eventEntryClass;

		return $this;
	}

	public function toEventType(): EventTypeInterface
	{
		$this->guardResolved();

		$clone = clone $this;
		$clone->resolved = true;

		return $clone;
	}

	private function guardResolved(): void
	{
		if ($this->resolved) {
			throw new InvalidArgumentException('This builder has already been resolved.');
		}
	}
}
