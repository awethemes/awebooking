<?php

namespace Awepointment\BusinessTime;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use InvalidArgumentException;

class BusinessHours implements \JsonSerializable
{
    /**
     * Weekdays
     */
    public const SUNDAY = 'sunday';
    public const MONDAY = 'monday';
    public const TUESDAY = 'tuesday';
    public const WEDNESDAY = 'wednesday';
    public const THURSDAY = 'thursday';
    public const FRIDAY = 'friday';
    public const SATURDAY = 'saturday';

    /**
     * 0 = Sunday, 1 = Monday, ... 6 = Saturday
     */
    public const WEEKDAYS = [
        self::SUNDAY,
        self::MONDAY,
        self::TUESDAY,
        self::WEDNESDAY,
        self::THURSDAY,
        self::FRIDAY,
        self::SATURDAY,
    ];

    /**
     * @var BusinessDay[]
     */
    protected $days = [];

    /**
     * @var Holidays
     */
    protected $holidays;

    /**
     * @var DateTimeZone
     */
    protected $timezone;

    /**
     * @var int
     */
    protected $startOfWeek;

    public function __construct($timezone = null, $startOfWeek = 0)
    {
        if ($timezone) {
            $timezone = $timezone instanceof DateTimeZone ? $timezone : new DateTimeZone($timezone);
        }

        $this->timezone = $timezone;
        $this->startOfWeek = $startOfWeek;

        $this->holidays = new Holidays();

        $this->days = array_map(static function () {
            return new BusinessDay();
        }, array_fill_keys(self::WEEKDAYS, []));
    }

    /**
     * @param string $weekday
     * @param TimeRange ...$timeRanges
     * @return $this
     */
    public function define(string $weekday, TimeRange ...$timeRanges): self
    {
        $this->getDay($weekday)->setTimeRange(...$timeRanges);

        return $this;
    }

    /**
     * Gets the BusinessHoursForDay corresponding to the weekday name.
     *
     * @param string $weekday
     * @return BusinessDay
     */
    public function getDay($weekday): BusinessDay
    {
        if ($weekday instanceof DateTimeInterface) {
            $weekday = BusinessHours::WEEKDAYS[(int) $weekday->format('w')];
        }

        if (!in_array($weekday, self::WEEKDAYS, true)) {
            throw new InvalidArgumentException(
                sprintf('Invalid day name, valid values: [%s]', implode(', ', self::WEEKDAYS))
            );
        }

        return $this->days[$weekday];
    }

    /**
     * @return BusinessDay[]
     */
    public function getDays(): array
    {
        return $this->days;
    }

    public function getHolidays(): Holidays
    {
        return $this->holidays;
    }

    public function isDayClosed($day)
    {
        if ($day instanceof DateTimeInterface) {
            if ($this->getHolidays()->isHoliday($day)) {
                return false;
            }

            $day = BusinessHours::WEEKDAYS[(int) $day->format('w')];
        }

        return $this->getDay($day)->isClosed();
    }

    public function isClosedAt(DateTimeInterface $date)
    {
        return !$this->isOpenAt($date);
    }

    public function isOpenAt(DateTimeInterface $date)
    {
        $date = $this->applyTimezone($date);

        if ($this->getHolidays()->isHoliday($date)) {
            return false;
        }

        $businessDay = $this->getDay(BusinessHours::WEEKDAYS[(int) $date->format('w')]);

        return $businessDay->isOpenAt(Time::parse($date->format('H:i')));
    }

    private function applyTimezone(DateTimeInterface $date)
    {
        if (!$date instanceof DateTimeImmutable) {
            $date = clone $date;
        }

        if ($this->timezone) {
            $date = $date->setTimezone($this->timezone);
        }

        return $date;
    }

    /**
     * Gets the business day after the day number.
     *
     * @param int $dayNumber
     * @return BusinessDay|null
     */
    private function getDayAfter($dayNumber): ?BusinessDay
    {
        $tmpDayNumber = $dayNumber;

        for ($i = 0; $i < 6; $i++) {
            $tmpDayNumber = (self::SUNDAY === $tmpDayNumber) ? self::MONDAY : ++$tmpDayNumber;

            if (null !== $day = $this->getDay($tmpDayNumber)) {
                return $day;
            }
        }

        return $this->getDay($dayNumber);
    }

    public function jsonSerialize()
    {
        return $this->days;
    }
}
