<?php
namespace AweBooking\Support;

use DatePeriod;
use League\Period\Period as League_Period;

class Period extends League_Period {
	/**
	 * Create date period.
	 *
	 * The date should be a string using
	 * ISO-8601 "Y-m-d" date format, eg: 2017-05-10.
	 *
	 * @param string|Carbonate $start_date Starting date point.
	 * @param string|Carbonate $end_date   Ending date point.
	 * @param bool             $strict     Optional, use strict mode.
	 */
	public function __construct( $start_date, $end_date, $strict = false ) {
		parent::__construct(
			Carbonate::create_datetime( $start_date ),
			Carbonate::create_datetime( $end_date )
		);

		$this->validate_period( $strict );
	}

	/**
	 * Returns the starting date point as Carbon.
	 *
	 * @return Carbon
	 */
	public function get_start_date() {
		$dt = $this->getStartDate();

		return new Carbonate( $dt->format( 'Y-m-d H:i:s.u' ), $dt->getTimeZone() );
	}

	/**
	 * Returns the ending datepoint as Carbon.
	 *
	 * @return Carbon
	 */
	public function get_end_date() {
		$dt = $this->getEndDate();

		return new Carbonate( $dt->format( 'Y-m-d H:i:s.u' ), $dt->getTimeZone() );
	}

	/**
	 * Get number of nights.
	 *
	 * @return int
	 */
	public function nights() {
		return (int) $this->getDateInterval()->format( '%r%a' );
	}

	/**
	 * Get DatePeriod object instance.
	 *
	 * @param  DateInterval|int|string $interval The interval.
	 * @param  int                     $option   See DatePeriod::EXCLUDE_START_DATE.
	 * @return DatePeriod
	 */
	public function get_period( $interval = '1 DAY', $option = 0 ) {
		return new DatePeriod( $this->get_start_date(), static::filterDateInterval( $interval ), $this->get_end_date(), $option );
	}

	/**
	 * Validate period for require minimum night(s).
	 *
	 * @param  integer $nights Minimum night(s) to required, default 1.
	 * @return void
	 *
	 * @throws \LogicException
	 */
	public function required_minimum_nights( $nights = 1 ) {
		if ( $this->nights() < $nights ) {
			throw new \LogicException( sprintf( esc_html__( 'The date period must be have minimum %d night(s).', 'awebooking' ), esc_html( $nights ) ) );
		}
	}

	/**
	 * Validate the period in strict.
	 *
	 * @param  bool $strict Strict mode validation past date.
	 * @return void
	 *
	 * @throws \RangeException
	 */
	protected function validate_period( $strict ) {
		if ( $strict && $this->isBefore( Carbonate::today() ) ) {
			throw new \RangeException( esc_html__( 'The date period must be greater or equal to the today.', 'awebooking' ) );
		}
	}
}
