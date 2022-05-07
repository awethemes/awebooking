<?php

namespace AweBooking\System\Calendar;

interface EventTypeInterface
{
	public const TYPE_FIXED_STATES = 'TYPE_FIXED_STATES';
	public const TYPE_ARBITRARY_STATES = 'TYPE_ARBITRARY_STATES';

	public const GRANULARITY_DAILY = 'DAILY';
	public const GRANULARITY_HOURLY = 'HOURLY';

	public function getCode(): string;

	public function getType(): string;

	public function getGranularity(): string;

	/**
	 * @return array<int>
	 */
	public function getAllowedStates(): array;

	public function getDefaultState(): int;

	public function getUnitEntryClass(): string;

	public function getEventEntryClass(): string;
}
