<?php
namespace AweBooking\Calendar\Period;

class Year extends Period_Unit implements \IteratorAggregate {
	/**
	 * The date interval specification for the period.
	 *
	 * @var string
	 */
	protected $interval = 'P1Y';

	/**
	 * Create a Year period.
	 *
	 * @param int|DateTimeInterface $year The year for the period.
	 */
	public function __construct( $year ) {
		if ( $year instanceof \DateTimeInterface ) {
			$year = (int) $year->format( 'Y' );
		}

		parent::__construct( static::validateYear( $year ) . '-01-01' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getIterator() {
		// @codingStandardsIgnoreLine
		$initial = new Month( $this->startDate );

		return $this->generate_iterator( $initial, function( $current, $initial ) {
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
