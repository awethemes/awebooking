<?php

namespace AweBooking\PMS\Traits;

use AweBooking\DateUtils;
use AweBooking\System\DateTime;
use AweBooking\Vendor\Cake\Chronos\Date;

trait InteractsWithDateTime
{
    /**
     * @param string|DateTime|Date $startDate
     * @param string|DateTime|Date $endDate
     * @param bool $asDate
     * @return array
     */
    protected function parseDateRange($startDate, $endDate, $asDate = false): array
    {
        return DateUtils::parseDateRange($startDate, $endDate, $asDate);
    }
}
