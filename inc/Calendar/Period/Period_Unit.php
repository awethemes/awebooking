<?php
namespace AweBooking\Calendar\Period;

abstract class Period_Unit extends Period {
	/**
	 * The date interval specification for the period.
	 *
	 * @var string
	 */
	protected $interval = 'P1D';

	/**
	 * Create a period.
	 *
	 * @param string|DateTime $start_date The start date point.
	 */
	public function __construct( $start_date ) {
		$start_date = static::filterDatePoint( $start_date );

		$interval = static::filterDateInterval( $this->get_iterator_interval() );

		parent::__construct( $start_date, $start_date->add( $interval ) );
	}

	/**
	 * The the DateInterval instance.
	 *
	 * @return \DateInterval
	 */
	public function get_iterator_interval() {
		return new \DateInterval( $this->interval );
	}
}
