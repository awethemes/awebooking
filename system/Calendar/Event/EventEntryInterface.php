<?php

namespace AweBooking\System\Calendar\Entry;

use AweBooking\System\Calendar\EventTypeInterface;
use DateTimeInterface;

interface EventEntryInterface
{
	public function getId();

	public function getUnit();

	public function getUnitId();

	public function setUnit($unit);

	public function getValue(): ?int;

	public function setValue(int $value);

	public function getEventType(): EventTypeInterface;

	public function setEventType(EventTypeInterface $eventType);

	public function setStartDate(DateTimeInterface $date);

	public function getStartDate(): ?DateTimeInterface;

	public function getEndDate(): ?DateTimeInterface;

	public function setEndDate(DateTimeInterface $date);

	public function getReference();

	public function setReference($model);
}
