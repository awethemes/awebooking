<?php
namespace AweBooking\Support;

use League\Period\Period as League_Period;

class Period extends League_Period {
	/**
	 * Create a period instance.
	 *
	 * @param mixed $start_date Starting date point.
	 * @param mixed $end_date   Ending date point.
	 * @return static
	 */
	public static function create( $start_date, $end_date ) {
		return new static( $start_date, $end_date );
	}

	/**
	 * Create date period.
	 *
	 * @param mixed $start_date Starting date point.
	 * @param mixed $end_date   Ending date point.
	 */
	public function __construct( $start_date, $end_date ) {
		parent::__construct(
			Carbonate::create_date_time( $start_date ),
			Carbonate::create_date_time( $end_date )
		);
	}

	/**
	 * Returns the starting date point.
	 *
	 * @return \AweBooking\Support\Carbonate
	 */
	public function get_start_date() {
		return Carbonate::create_date_time( $this->getStartDate() );
	}

	/**
	 * Returns the ending datepoint.
	 *
	 * @return \AweBooking\Support\Carbonate
	 */
	public function get_end_date() {
		return Carbonate::create_date_time( $this->getEndDate() );
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
	 * Split period only days.
	 *
	 * @param  array $days Split only their days.
	 * @return \AweBooking\Support\Period_Collection
	 */
	public function split_only_days( $days = [] ) {
		$periods = Period_Collection::make( $this->getIterator() );

		if ( ! empty( $periods ) ) {
			$periods = $periods->reject( function( $date ) use ( $days ) {
				return ! in_array( $date->dayOfWeek, (array) $days ); // @codingStandardsIgnoreLine
			});
		}

		return $periods;
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
	 * Getter class property.
	 *
	 * @param  string $property The property name.
	 * @return mixed
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __get( $property ) {
		switch ( $property ) {
			case 'days':
				return (int) $this->getDateInterval()->format( '%r%a' );
			case 'start_date':
				return $this->get_start_date();
			case 'end_date':
				return $this->get_end_date();
		}

		throw new \InvalidArgumentException( "Unknown getter '{$property}'" );
	}
}
