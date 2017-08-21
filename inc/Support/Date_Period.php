<?php
namespace AweBooking\Support;

use DateTimeImmutable;
use League\Period\Period;

class Date_Period extends Period {
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
		// Back-compat with League.Period when create static class in some methods.
		// The League.Period using `DateTimeImmutable`, so we don't parse it if have.
		if ( ! $start_date instanceof DateTimeImmutable ) {
			$start_date = Carbonate::create_date( $start_date );
		}

		if ( ! $end_date instanceof DateTimeImmutable ) {
			$end_date = Carbonate::create_date( $end_date );
		}

		// Call parent constructor,
		// then call validate the date period.
		parent::__construct( $start_date, $end_date );

		// We need a period required minimum one night or
		// not be the past days if "strict" passed as true.
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
	 * @param  int $option See DatePeriod::EXCLUDE_START_DATE.
	 * @return DatePeriod
	 */
	public function get_period( $option = 0 ) {
		return $this->getDatePeriod( '1 day', $option );
	}

	/**
	 * Validate period for require minimum night(s).
	 *
	 * @param  integer $nights Minimum night(s) to required, default 1.
	 * @return void
	 *
	 * @throws \LogicException
	 */
	public function require_minimum_nights( $nights = 1 ) {
		if ( $this->nights() < $nights ) {
			throw new \LogicException( sprintf( esc_html__( 'The date period must be have minimum %s night(s).', 'awebooking' ), $nights ) );
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
