<?php
namespace AweBooking\Calendar\Period;

class Month extends Period_Unit implements \IteratorAggregate {
	/**
	 * The date interval specification for the period.
	 *
	 * @var string
	 */
	protected $interval = 'P1M';

	/**
	 * Create a Month period.
	 *
	 * @param int|\DateTimeInterface $year  The year for the period.
	 * @param int                    $month The month of year, optional.
	 */
	public function __construct( $year, $month = null ) {
		if ( $year instanceof \DateTimeInterface ) {
			$month = (int) $year->format( 'n' );
			$year  = (int) $year->format( 'Y' );
		}

		parent::__construct( sprintf( '%d-%02d-01',
			static::validateYear( $year ),
			static::validateRange( $month, 1, 12 )
		) );
	}

	/**
	 * Get number days in month.
	 *
	 * @return int
	 */
	public function get_number_days() {
		// @codingStandardsIgnoreLine
		return (int) $this->get_start_date()->daysInMonth;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getIterator() {
		$initial = new Week( $this->get_start_date()->startOfWeek() );

		return $this->generate_iterator( $initial, function( $current, $_initial ) {
			return $current->sameValueAs( $_initial ) || $this->contains( $current->get_start_date() );
		});
	}

	/**
	 * Return a string representation of this Period
	 *
	 * @return string
	 */
	public function __toString() {
		// @codingStandardsIgnoreLine
		return $this->startDate->format( 'n' );
	}
}
