<?php
namespace AweBooking\Calendar\Period;

use AweBooking\Support\Carbonate;

class Day extends Period_Unit {
	/**
	 * The date interval specification for the period.
	 *
	 * @var string
	 */
	protected $interval = 'P1D';

	/**
	 * Create a Date period.
	 *
	 * @param string|DateTime $start_date The start date point.
	 */
	public function __construct( $start_date ) {
		parent::__construct( Carbonate::create_date( $start_date ) );
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
}
