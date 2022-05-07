<?php

namespace AweBooking\PMS\Helpers;

use AweBooking\System\DateTime;
use Isolated\Cake\Chronos\ChronosInterface;
use Isolated\Cake\Chronos\Date;
use InvalidArgumentException;
use LogicException;
use Throwable;

class DateRange
{
    /**
     * @var ChronosInterface
     */
    public $startDate;

    /**
     * @var ChronosInterface
     */
    public $endDate;

    /**
     * @param ChronosInterface $startDate
     * @param ChronosInterface $endDate
     * @param bool $asDate
     */
    public function __construct(
        ChronosInterface $startDate,
        ChronosInterface $endDate,
        bool $asDate = false
    ) {
        if ($startDate > $endDate) {
            throw new LogicException('Start date cannot be greater than end date.');
        }

        $this->startDate = $asDate ? Date::instance($startDate) : $startDate;
        $this->endDate = $asDate ? Date::instance($endDate) : $endDate;
    }

    /**
     * @param string|DateTime $startDate
     * @param string|DateTime $endDate
     * @param bool $asDate
     * @return static
     */
    public static function parse($startDate, $endDate, bool $asDate = false): self
    {
        try {
            [$startDate, $endDate] = [
                DateTime::createFromValue($startDate),
                DateTime::createFromValue($endDate),
            ];

            return new self($startDate, $endDate, $asDate);
        } catch (Throwable $e) {
            throw new LogicException('Invalid date range.');
        }
    }

    /**
     * @param array $data
     * @param bool $asDate
     * @return static
     */
    public static function fromArray(array $data, bool $asDate = false): self
    {
        $startDate = $data['startDate'] ?? $data['start_date'] ?? $data['start'] ?? null;

        $endDate = $data['endDate'] ?? $data['end_date'] ?? $data['end'] ?? null;

        if (empty($startDate) || empty($endDate)) {
            throw new InvalidArgumentException('Invalid date range.');
        }

        return self::parse($startDate, $endDate, $asDate);
    }

    /**
     * @return static
     */
    public static function thisMonth(bool $asDate = false): self
    {
        return new self(
            DateTime::now()->startOfMonth(),
            DateTime::now()->endOfMonth(),
            $asDate
        );
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->startDate . ' - ' . $this->endDate;
    }
}
