<?php

namespace AweBooking\BusinessTime;

use AweBooking\BusinessTime\Exceptions\InvalidTimeString;
use AweBooking\System\DateTime;
use DateTimeInterface;
use DateTimeZone;

class Time
{
    /** @var int */
    protected $hours;

    /** @var int */
    protected $minutes;

    public static function parse(string $string): self
    {
        if (!preg_match('/^(([0-1][0-9]|2[0-3]):[0-5][0-9]|24:00)$/', $string)) {
            throw new InvalidTimeString(
                "The string `{$string}` isn't a valid time string. A time string must be a formatted as `H:i`, e.g. `06:00`, `18:00`."
            );
        }

        [$hours, $minutes] = explode(':', $string);

        return new self($hours, $minutes);
    }

    private function __construct(int $hours, int $minutes)
    {
        $this->hours = $hours;
        $this->minutes = $minutes;
    }

    public function getHours(): int
    {
        return $this->hours;
    }

    public function getMinutes(): int
    {
        return $this->minutes;
    }

    public function isSameOrAfter(Time $startTime)
    {
        return $this->isAfter($startTime) || $this->isSame($startTime);
    }

    public function isSameOrBefore(Time $endTime)
    {
        return $this->isBefore($endTime) || $this->isSame($endTime);
    }

    public function isSame(self $time): bool
    {
        return $this->hours === $time->hours && $this->minutes === $time->minutes;
    }

    public function isAfter(self $time): bool
    {
        if ($this->isSame($time)) {
            return false;
        }

        if ($this->hours > $time->hours) {
            return true;
        }

        return $this->hours === $time->hours && $this->minutes >= $time->minutes;
    }

    public function isBefore(self $time): bool
    {
        if ($this->isSame($time)) {
            return false;
        }

        return !$this->isAfter($time);
    }

    public function asDateTime(DateTimeInterface $date = null): DateTimeInterface
    {
        $date = $date
            ? DateTime::parse($date)
            : new DateTime('1970-01-01 00:00:00');

        return $date->setTime($this->hours, $this->minutes);
    }

    public function format(string $format = 'H:i', $timezone = null): string
    {
        if ($timezone) {
            $timezone = $timezone instanceof DateTimeZone
                ? $timezone
                : new DateTimeZone($timezone);
        }

        $date = $timezone
            ? new DateTime('1970-01-01 00:00:00', $timezone)
            : null;

        return $this->asDateTime($date)->format($format);
    }

    public function __toString(): string
    {
        return $this->format();
    }
}
