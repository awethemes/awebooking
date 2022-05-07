<?php

namespace AweBooking\System\Calendar;

use AweBooking\System\Calendar\Entry\EventEntry;
use AweBooking\System\Database\Model;

class EventType implements EventTypeInterface
{
	/**
	 * @var string
	 */
	protected $code;

	/**
	 * @var string
	 */
	protected $type;

	/**
	 * @var string
	 */
	protected $granularity;

	/**
	 * @var int
	 */
	protected $defaultState;

	/**
	 * @var array<int>
	 */
	protected $allowedStates;

	/**
	 * @var string|class-string<Model>
	 */
	protected $unitEntryClass;

	/**
	 * @var string|class-string<EventEntry>
	 */
	protected $eventEntryClass;

	public function __construct(
		string $code,
		string $type,
		string $granularity,
		int $defaultState = 0,
		array $allowedStates = [],
		string $unitEntryClass = null,
		string $eventEntryClass = EventEntry::class
	) {
		$this->code = $code;
		$this->type = $type;
		$this->granularity = $granularity;
		$this->defaultState = $defaultState;
		$this->allowedStates = $allowedStates;
		$this->unitEntryClass = $unitEntryClass;
		$this->eventEntryClass = $eventEntryClass;
	}

	public function getCode(): string
	{
		return $this->code;
	}

	public function getType(): string
	{
		return $this->type;
	}

	public function getGranularity(): string
	{
		return $this->granularity;
	}

	public function getAllowedStates(): array
	{
		return $this->allowedStates;
	}

	public function getDefaultState(): int
	{
		return $this->defaultState;
	}

	public function getUnitEntryClass(): string
	{
		return $this->unitEntryClass;
	}

	public function getEventEntryClass(): string
	{
		return $this->unitEntryClass;
	}
}
