<?php

namespace AweBooking\Calendar\Period;

class Day extends Iterator_Period {
	/**
	 * The date interval specification for the period.
	 *
	 * @var string
	 */
	protected $interval = 'P1D';

	/**
	 * Create a Date period.
	 *
	 * @param \DateTime|string $start_date The start date point.
	 */
	public function __construct( $start_date ) {
		$start_date = abrs_date_time( $start_date );

		list( $start_date, $end_date ) = $this->filter_from_datepoint( $start_date );

		parent::__construct( $start_date, $end_date );
	}

	/**
	 * Return a string representation of this Period
	 *
	 * @return string
	 */
	public function __toString() {
		// @codingStandardsIgnoreLine
		return $this->startDate->format( 'j' );
	}

	/**
	 * Forward getter from start-date.
	 *
	 * @param  string $name Get key name.
	 * @return mixed
	 */
	public function __get( $name ) {
		try {
			return parent::__get( $name );
		} catch ( \InvalidArgumentException $e ) {
			return $this->get_start_date()->{$name};
		}
	}
}
