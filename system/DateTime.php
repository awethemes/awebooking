<?php

namespace AweBooking\System;

use AweBooking\System\Cake\Chronos\Chronos;
use AweBooking\System\Cake\Chronos\ChronosInterface;
use DateTimeInterface;

class DateTime extends Chronos
{
    /**
     * Determine if the given value is a standard date format.
     *
     * @param string $value
     * @return bool
     */
    public static function isStandardDateFormat($value): bool
    {
        return preg_match('/^(\\d{4})-(\\d{1,2})-(\\d{1,2})$/', $value);
    }

    /**
     * @param mixed $value
     * @param string|null $format
     * @param \DateTimeZone|string|null $tz
     * @return DateTime|ChronosInterface
     */
    public static function createFromValue($value, $format = null, $tz = null)
    {
        // If this value is already a Chronos instance, we shall just return it as is.
        // This prevents us having to re-instantiate a Chronos instance when we know
        // it already is one, which wouldn't be fulfilled by the DateTime check.
        if ($value instanceof ChronosInterface) {
            return static::instance($value);
        }

        // If the value is already a DateTime instance, we will just skip the rest of
        // these checks since they will be a waste of time, and hinder performance
        // when checking the field. We will just return the DateTime right away.
        if ($value instanceof DateTimeInterface) {
            return static::parse($value->format('Y-m-d H:i:s.u'), $value->getTimezone());
        }

        // If this value is an integer, we will assume it is a UNIX timestamp's value
        // and format a Chronos object from this timestamp. This allows flexibility
        // when defining your date fields as they might be UNIX timestamps here.
        if (is_numeric($value)) {
            return static::createFromTimestamp($value, $tz);
        }

        // If the value is in simply year, month, day format, we will instantiate the
        // Chronos instances from that format. Again, this provides for simple date
        // fields on the database, while still supporting Chronos conversion.
        if (static::isStandardDateFormat($value)) {
            return static::instance(static::createFromFormat('Y-m-d', $value, $tz)->startOfDay());
        }

        if ($format !== null) {
            // @see https://bugs.php.net/bug.php?id=75577
            if (version_compare(PHP_VERSION, '7.3.0-dev', '<')) {
                $format = str_replace('.v', '.u', $format);
            }

            return static::createFromFormat($format, $value, $tz);
        }

        return static::parse($value, $tz);
    }
}
