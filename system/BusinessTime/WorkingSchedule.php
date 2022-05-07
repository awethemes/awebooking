<?php

namespace Awepointment\BusinessTime;

use DateTimeInterface;

final class WorkingSchedule extends BusinessHours
{
    protected $timeOff;

    public function isWorkingAt(DateTimeInterface $date)
    {
        return true;

        return $this->isOpenAt($date);
    }
}
