<?php

namespace AweBooking\BusinessTime;

use AweBooking\BusinessTime\Exceptions\InvalidTimeRangeString;
use JsonSerializable;

class TimeRange implements JsonSerializable
{
    /**
     * @var Time
     */
    protected $start;

    /**
     * @var Time
     */
    protected $end;

    public static function create(string $start, string $end): self
    {
        return new self(Time::parse($start), Time::parse($end));
    }

    public static function fromString(string $string): self
    {
        $times = explode('-', $string);

        if (count($times) !== 2) {
            throw new InvalidTimeRangeString(
                'Invalid time range string. A time range string must be a formatted as `H:i-H:i`'
            );
        }

        return self::create($times[0], $times[1]);
    }

    public function __construct(Time $start, Time $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function getStart(): Time
    {
        return $this->start;
    }

    public function getEnd(): Time
    {
        return $this->end;
    }

    public function containsTime(Time $time): bool
    {
        return $time->isSameOrAfter($this->start)
               && ($this->isOverflowsNextDay() || $time->isBefore($this->end));
    }

    public function containsNightTime(Time $time): bool
    {
        if (!$this->isOverflowsNextDay()) {
            return false;
        }

        $midnight = new static(Time::parse('00:00'), $this->getEnd());

        return $midnight->containsTime($time);
    }

    public function overlaps(self $timeRange): bool
    {
        return $this->containsTime($timeRange->start)
               || $this->containsTime($timeRange->end);
    }

    public function isOverflowsNextDay(): bool
    {
        return $this->start->isAfter($this->end);
    }

    public function format(
        string $timeFormat = 'H:i',
        string $rangeFormat = '%s-%s',
        $timezone = null
    ): string {
        return sprintf(
            $rangeFormat,
            $this->start->format($timeFormat, $timezone),
            $this->end->format($timeFormat, $timezone)
        );
    }

    public function __toString(): string
    {
        return $this->format();
    }

    public function jsonSerialize()
    {
        return [
            'start_time' => $this->start->format(),
            'end_time' => $this->end->format(),
        ];
    }
}
