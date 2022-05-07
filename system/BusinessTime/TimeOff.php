<?php

namespace AweBooking\BusinessTime;

class TimeOff extends TimeRange
{
    /**
     * @var string
     */
    public $reason;

    /**
     * @param string $reason
     * @return $this
     */
    public function setReason(string $reason)
    {
        $this->reason = $reason;

        return $this;
    }
}
