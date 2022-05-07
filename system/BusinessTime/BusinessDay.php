<?php

namespace Awepointment\BusinessTime;

use ArrayIterator;
use Awepointment\BusinessTime\Exceptions\OverlappingTimeRanges;
use Countable;
use IteratorAggregate;
use JsonSerializable;

class BusinessDay implements Countable, IteratorAggregate, JsonSerializable
{
    /**
     * @var TimeRange[]
     */
    protected $timeRanges = [];

    public function setTimeRange(TimeRange ...$timeRanges)
    {
        foreach ($timeRanges as $timeRange) {
            $this->timeRanges[] = $timeRange;
        }

        usort($this->timeRanges, static function ($a, $b) {
            return $a->getStart()->isAfter($b->getStart()) ? 1 : -1;
        });

        self::guardAgainstTimeRangeOverlaps($this->timeRanges);

        return $this;
    }

    public function isClosed()
    {
        return $this->count() === 0;
    }

    public function isOpenAt(Time $time)
    {
        foreach ($this->timeRanges as $timeRange) {
            if ($timeRange->containsTime($time)) {
                return true;
            }
        }

        return false;
    }

    public function isOpenAtNight(Time $time)
    {
        foreach ($this->timeRanges as $timeRange) {
            if ($timeRange->containsNightTime($time)) {
                return true;
            }
        }

        return false;
    }

    public function count()
    {
        return count($this->timeRanges);
    }

    public function getTimeRanges()
    {
        return $this->timeRanges;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->timeRanges);
    }

    public function jsonSerialize()
    {
        return $this->timeRanges;
    }

    public static function guardAgainstTimeRangeOverlaps(array $timeRanges)
    {
        // Create an unique pairs each time range.
        // E.g: [1, 2, 3] => [[1, 2], [1, 3], [2, 3]]
        while ($a = array_shift($timeRanges)) {
            foreach ($timeRanges as $b) {
                if ($a->overlaps($b)) {
                    throw new OverlappingTimeRanges("Time ranges {$a} and {$b} overlap.");
                }
            }
        }
    }
}
