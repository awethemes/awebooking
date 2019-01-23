<?php

namespace AweBooking\Calendar\Period;

class Year extends Iterator_Period {
	/**
	 * The date interval specification for the period.
	 *
	 * @var string
	 */
	protected $interval = 'P1Y';

	/**
	 * Create a Year period.
	 *
	 * @param int|\DateTime $year The year for the period.
	 */
	public function __construct( $year ) {
		if ( $year instanceof \DateTimeInterface ) {
			$year = (int) $year->format( 'Y' );
		}

		list( $start_date, $end_date ) = $this->filter_from_datepoint(
			static::validateYear( $year ) . '-01-01'
		);

		parent::__construct( $start_date, $end_date );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getIterator() {
		// @codingStandardsIgnoreLine
		$initial = new Month( $this->startDate );

		return $this->generate_iterator( $initial, function( $current, $initial ) {
			/* @var \AweBooking\Calendar\Period\Iterator_Period $current */
			return $this->contains( $current->get_start_date() );
		});
	}

	/**
	 * Return a string representation of this Period
	 *
	 * @return string
	 */
	public function __toString() {
		// @codingStandardsIgnoreLine
		return $this->startDate->format( 'Y' );
	}
}
