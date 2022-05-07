<?php

namespace AweBooking\BusinessTime;

use AweBooking\BusinessTime\Exceptions\BusinessTimeException;
use AweBooking\Models\Staff;
use AweBooking\Models\StaffSchedule;
use AweBooking\Vendor\Illuminate\Database\Eloquent\Collection;
use Awepointment\Vendor\Illuminate\Support\Arr;
use Throwable;

class Factory
{
    public static function createFromRawData(array $data): BusinessHours
    {
        $businessTime = new BusinessHours();

        foreach ($data as $weekday => $hours) {
            if (!in_array($weekday, BusinessHours::WEEKDAYS, true)) {
                continue;
            }

            $timeRanges = [];

            // $hours is an array of arrays.
            if (is_array($hours) && !Arr::isAssoc($hours)) {
                foreach ($hours as $_timeRange) {
                    if ($timeRange = self::createTimeRangeFromRawValue($_timeRange)) {
                        $timeRanges[] = $timeRange;
                    }
                }
            } elseif ($timeRange = self::createTimeRangeFromRawValue($hours)) {
                $timeRanges[] = $timeRange;
            }

            if (count($timeRanges) > 0) {
                $businessTime->define($weekday, ...$timeRanges);
            }
        }

        return $businessTime;
    }

    public static function createTimeRangeFromRawValue($hours): ?TimeRange
    {
        // $hours is an string of time-ranges (09:00-17:00).
        if (is_string($hours)) {
            try {
                return TimeRange::fromString($hours);
            } catch (BusinessTimeException $e) {
                return null;
            }
        }

        // $hours is an array of time-ranges with start_time and end_time.
        // E.x: 'monday' => ['start_time' => '09:00', 'end_time' => '17:00']
        if (is_array($hours) && isset($hours['start_time'], $hours['end_time'])) {
            try {
                return TimeRange::create($hours['start_time'], $hours['end_time']);
            } catch (BusinessTimeException $e) {
                return null;
            }
        }

        return null;
    }

    public static function createStaffWorkingSchedule(Staff $staff): WorkingSchedule
    {
        /** @var Collection|StaffSchedule[] $schedules */
        $schedules = $staff->staffSchedules->groupBy('weekday');

        $businessTime = new WorkingSchedule();

        foreach ($schedules as $weekday => $_schedules) {
            if (!in_array($weekday, BusinessHours::WEEKDAYS, true)) {
                continue;
            }

            if (
                count($_schedules) === 0
                || (count($_schedules) === 1 && $_schedules[0]->is_closed)
            ) {
                continue;
            }

            $timeRanges = $_schedules
                ->sortBy('shift')
                ->map(function (StaffSchedule $schedule) {
                    try {
                        return TimeRange::create($schedule->start_time, $schedule->end_time);
                    } catch (Throwable $e) {
                        return null;
                    }
                })->filter()->values();

            $businessTime->define($weekday, ...$timeRanges);
        }

        return $businessTime;
    }
}
