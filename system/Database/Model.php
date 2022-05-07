<?php

namespace AweBooking\System\Database;

use AweBooking\System\DateTime;
use AweBooking\Vendor\Illuminate\Database\Eloquent\Model as BaseModel;
use DateTimeInterface;

class Model extends BaseModel
{
    /**
     * @return DateTime
     */
    public function freshTimestamp()
    {
        return DateTime::now();
    }

    /**
     * @param mixed $value
     * @return DateTime
     */
    public function asDateTime($value)
    {
        return DateTime::createFromValue($value);
    }

    /**
     * {@inheritdoc}
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return DateTime::instance($date)->toIso8601String();
    }
}
