<?php
namespace AweBooking\Calendar\Period;

use DatePeriod;
use AweBooking\Support\Carbonate;
use League\Period\Period as League_Period;

class Period extends League_Period implements \IteratorAggregate {
	/**
	 * Create date period.
	 *
	 * The date should be a string using
	 * ISO-8601 "Y-m-d" date format, eg: 2017-05-10.
	 *
	 * @param string|Carbonate $start_date Starting date point.
	 * @param string|Carbonate $end_date   Ending date point.
	 */
	public function __construct( $start_date, $end_date ) {
		parent::__construct(
			Carbonate::create_datetime( $start_date ),
			Carbonate::create_datetime( $end_date )
		);
	}

	/**
	 * Returns the starting date point.
	 *
	 * @return Carbonate
	 */
	public function get_start_date() {
		return $this->getStartDate();
	}

	/**
	 * Returns the ending datepoint.
	 *
	 * @return Carbonate
	 */
	public function get_end_date() {
		return $this->getEndDate();
	}

	/**
	 * Tells whether the specified index is fully contained within
	 * the current Period object.
	 *
	 * @param  DateTimeInterface|string $index The datetime index.
	 * @return bool
	 */
	public function contains( $index ) {
		return parent::contains( $index );
	}

	/**
	 * Returns true if the period include the other period
	 * given as argument.
	 *
	 * @param  PeriodInterface $period The period instance.
	 * @return bool
	 */
	public function includes( PeriodInterface $period ) {
		return $this->containsPeriod( $period );
	}

	/**
	 * Returns if $event is during this period.
	 *
	 * @param  Event_Interface $event The event instance.
	 * @return bool
	 */
	public function contains_event( Event_Interface $event ) {
		// TODO: ...
		return false;
	}

	/**
	 * Format the period at start date point.
	 *
	 * @param  string $format The format string.
	 * @return string
	 */
	public function format( $format ) {
		return $this->get_start_date()->format( $format );
	}

	/**
	 * Get DatePeriod object instance.
	 *
	 * @param  DateInterval|int|string $interval The interval.
	 * @param  int                     $option   See DatePeriod::EXCLUDE_START_DATE.
	 * @return DatePeriod
	 */
	public function get_date_period( $interval = '1 DAY', $option = 0 ) {
		return new DatePeriod( $this->get_start_date(), static::filterDateInterval( $interval ), $this->get_end_date(), $option );
	}

	/**
	 * Returns the iterator interval.
	 *
	 * @return \DateInterval
	 */
	public function get_iterator_interval() {
		return new \DateInterval( 'P1D' );
	}

	/**
	 * Generate the iterator.
	 *
	 * @param  Period   $initial  The initial value.
	 * @param  callable $callback The callback, must be returned "false" sometimes for stop the while loop.
	 * @return \Generator
	 */
	protected function generate_iterator( Period $initial, callable $callback ) {
		$current = $initial;

		while ( $callback( $current, $initial ) ) {
			yield (string) $current => $current;

			$current = $current->next( $current->get_iterator_interval() );
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getIterator() {
		// @codingStandardsIgnoreLine
		$initial = new Day( $this->startDate );

		return $this->generate_iterator( $initial, function( $current, $initial ) {
			return $this->contains( $current->get_start_date() );
		});
	}

	/**
	 * {@inheritdoc}
	 */
	public function getStartDate() {
		// @codingStandardsIgnoreLine
		$dt = $this->startDate;

		return new Carbonate( $dt->format( 'Y-m-d H:i:s.u' ), $dt->getTimeZone() );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getEndDate() {
		// @codingStandardsIgnoreLine
		$dt = $this->endDate;

		return new Carbonate( $dt->format( 'Y-m-d H:i:s.u' ), $dt->getTimeZone() );
	}
}
