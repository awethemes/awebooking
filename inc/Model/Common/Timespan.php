<?php
namespace AweBooking\Model\Common;

use AweBooking\Support\Period;
use AweBooking\Support\Carbonate;

class Timespan {
	/**
	 * The start-date.
	 *
	 * @var \AweBooking\Support\Carbonate
	 */
	protected $start_date;

	/**
	 * The end-date.
	 *
	 * @var \AweBooking\Support\Carbonate
	 */
	protected $end_date;

	/**
	 * Create new timespan from a date-point.
	 *
	 * @param  Carbonate|string|int $start_date The start date.
	 * @param  int                  $nights     The number of nights to the end-date.
	 * @return static
	 */
	public static function from( $start_date, $nights = 1 ) {
		$start_date = Carbonate::create_date_time( $start_date );

		return new static( $start_date, $start_date->copy()->addDays( $nights ) );
	}

	/**
	 * Create new instance from period.
	 *
	 * @param  \AweBooking\Support\Period $period The period.
	 * @return static
	 */
	public static function from_period( Period $period ) {
		return new static( $period->get_start_date(), $period->get_end_date() );
	}

	/**
	 * Constructor.
	 *
	 * @param Carbonate|string|int $start_date The start date.
	 * @param Carbonate|string|int $end_date   The end-date.
	 */
	public function __construct( $start_date, $end_date ) {
		$start_date = Carbonate::create_date_time( $start_date );
		$end_date = Carbonate::create_date_time( $end_date );

		static::validate_timespan( $start_date, $end_date );

		$this->start_date = $start_date;

		$this->end_date = $end_date;
	}

	/**
	 * Gets the start-date.
	 *
	 * @return \AweBooking\Support\Carbonate
	 */
	public function get_start_date() {
		return $this->start_date;
	}

	/**
	 * Gets the end-date.
	 *
	 * @return \AweBooking\Support\Carbonate
	 */
	public function get_end_date() {
		return $this->end_date;
	}

	/**
	 * Sets the start-date.
	 *
	 * @param Carbonate|string|int $start_date The start_date.
	 * @return $this
	 */
	public function set_start_date( $start_date ) {
		static::validate_timespan( $start_date, $this->end_date );

		$this->start_date = Carbonate::create_date_time( $start_date );

		return $this;
	}

	/**
	 * Sets the end-date.
	 *
	 * @param Carbonate|string|int $end_date The end_date.
	 * @return $this
	 */
	public function set_end_date( $end_date ) {
		static::validate_timespan( $this->start_date, $end_date );

		$this->end_date = Carbonate::create_date_time( $end_date );

		return $this;
	}

	/**
	 * Get nights stayed.
	 *
	 * @return int
	 */
	public function nights() {
		return (int) $this->to_period()->getDateInterval()->format( '%r%a' );
	}

	/**
	 * Returns the timespan as a period.
	 *
	 * @return \AweBooking\Support\Period
	 */
	public function to_period() {
		return Period::create( $this->start_date, $this->end_date );
	}

	/**
	 * Validate period for require minimum night(s).
	 *
	 * @param  integer $nights Minimum night(s) to required, default 1.
	 * @return $this
	 *
	 * @throws \LogicException
	 */
	public function minimum_nights( $nights = 1 ) {
		if ( $this->nights() < $nights ) {
			/* translators: %d: Number of nights */
			throw new \LogicException( sprintf( esc_html__( 'The reservation requires at least minimum %d night(s)', 'awebooking' ), esc_html( $nights ) ) );
		}

		return $this;
	}

	/**
	 * Perform validate the timespan.
	 *
	 * @param Carbonate $start_date The start-date.
	 * @param Carbonate $end_date   The end-date.
	 *
	 * @return void
	 * @throws \LogicException
	 */
	protected static function validate_timespan( Carbonate $start_date, Carbonate $end_date ) {
		if ( $start_date->gt( $end_date ) ) {
			throw new \LogicException( esc_html__( 'The check-in datepoint must be greater or equal to the check-out datepoint', 'awebooking' ) );
		}
	}
}
