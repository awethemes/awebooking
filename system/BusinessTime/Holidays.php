<?php

namespace AweBooking\BusinessTime;

use AweBooking\Vendor\Cake\Chronos\Date;
use DateTimeInterface;
use JsonSerializable;

class Holidays implements JsonSerializable
{
    /**
     * @var DateObjectStorage|Date[]
     */
    protected $holidays;

    /**
     * Creates a new holiday collection.
     *
     * @param \DateTimeInterface[] $holidays
     */
    public function __construct(array $holidays = [])
    {
        $this->holidays = new DateObjectStorage('Y-m-d');

        $this->addHolidays($holidays);
    }

    /**
     * Checks if a given date is holiday.
     *
     * @param DateTimeInterface $date
     * @return bool
     */
    public function isHoliday(DateTimeInterface $date): bool
    {
        return $this->holidays->contains($date);
    }

    /**
     * Adds a day.
     *
     * @param DateTimeInterface $holiday
     */
    public function addHoliday(DateTimeInterface $holiday)
    {
        if (!$holiday instanceof Date) {
            $holiday = Date::instance($holiday);
        }

        $this->holidays->attach($holiday);
    }

    /**
     * Adds a set of days.
     *
     * @param DateTimeInterface[] $holidays
     */
    public function addHolidays($holidays)
    {
        foreach ($holidays as $holiday) {
            $this->addHoliday($holiday);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return array_map(static function ($date) {
            return $date->format('Y-m-d');
        }, iterator_to_array($this->holidays));
    }
}
