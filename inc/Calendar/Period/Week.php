<?php
namespace AweBooking\Calendar\Period;

use AweBooking\Support\Carbonate;

class Week extends Iterator_Period {
	/**
	 * The date interval specification for the period.
	 *
	 * @var string
	 */
	protected $interval = 'P1W';

	/**
	 * Create a Week period.
	 *
	 * @param string|DateTime $start_date The start date point.
	 */
	public function __construct( $start_date ) {
		$start_date = Carbonate::create_date( $start_date )->startOfWeek();

		list( $start_date, $end_date ) = $this->filter_from_datepoint( $start_date );

		parent::__construct( $start_date, $end_date );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getIterator() {
		// @codingStandardsIgnoreLine
		$initial = new Day( $this->startDate );

		return $this->generate_iterator( $initial, function( $current ) {
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
		return $this->startDate->format( 'W' );
	}
}
