<?php
namespace AweBooking\Model\Common;

use JsonSerializable;
use AweBooking\Constants;
use AweBooking\Support\Period;
use AweBooking\Support\Carbonate;
use AweBooking\Support\Traits\Fluent_Getter;

class Timespan implements JsonSerializable {
	use Fluent_Getter;

	/**
	 * The period.
	 *
	 * @var string
	 */
	protected $period;

	/**
	 * Create new instance from a date point with number of nights.
	 *
	 * @param  mixed $start_date The start date.
	 * @param  int   $nights     Number of nights.
	 * @return static
	 */
	public static function from( $start_date, $nights = 1 ) {
		$start_date = Carbonate::create_date( $start_date );

		return new static( $start_date, $start_date->copy()->addDays( $nights ) );
	}

	/**
	 * Create new instance from a Period.
	 *
	 * @param  \AweBooking\Support\Period $period The period instance.
	 * @return static
	 */
	public static function from_period( Period $period ) {
		return new static( $period->get_start_date(), $period->get_end_date() );
	}

	/**
	 * Constructor.
	 *
	 * @param mixed $start_date The start date point.
	 * @param mixed $end_date   The end date point.
	 */
	public function __construct( $start_date, $end_date ) {
		$this->period = new Period(
			Carbonate::create_date( $start_date ),
			Carbonate::create_date( $end_date )
		);
	}

	/**
	 * Returns a new Timespan with a new starting date point.
	 *
	 * @param  mixed $start_date The start date point.
	 * @return static
	 */
	public function starting_on( $start_date ) {
		return new static( Carbonate::create_date( $start_date ), $this->get_end_date() );
	}

	/**
	 * Returns a new Timespan with a new ending date point.
	 *
	 * @param  mixed $end_date The end date point.
	 * @return static
	 */
	public function ending_on( $end_date ) {
		return new static( $this->get_start_date(), Carbonate::create_date( $end_date ) );
	}

	/**
	 * Returns the start date as string.
	 *
	 * @return string
	 */
	public function get_start_date() {
		return $this->period->start_date->format( 'Y-m-d' );
	}

	/**
	 * Returns the end date as string.
	 *
	 * @return string
	 */
	public function get_end_date() {
		return $this->period->end_date->format( 'Y-m-d' );
	}

	/**
	 * Gets the number of nights.
	 *
	 * @return int
	 */
	public function get_nights() {
		return (int) $this->period->getDateInterval()->format( '%r%a' );
	}

	/**
	 * Alias of get_nights method.
	 *
	 * @return int
	 */
	public function nights() {
		return $this->get_nights();
	}

	/**
	 * Returns the timespan as a period.
	 *
	 * @param  string $granularity The granularity level.
	 * @return \AweBooking\Support\Period
	 */
	public function to_period( $granularity = Constants::GL_DAILY ) {
		return ( Constants::GL_NIGHTLY === $granularity )
			? $this->period->moveEndDate( '-1 minute' )
			: $this->period->moveEndDate( '+23 hours 59 minutes' );
	}

	/**
	 * Convert the timespan to an array.
	 *
	 * @return array
	 */
	public function to_array() {
		return [
			'nights'     => $this->get_nights(),
			'start_date' => $this->get_start_date(),
			'end_date'   => $this->get_end_date(),
		];
	}

	/**
	 * Convert the object into something JSON serializable.
	 *
	 * @return array
	 */
	public function jsonSerialize() {
		return $this->to_array();
	}

	/**
	 * Validate period for requires minimum nights.
	 *
	 * @param  integer $nights Number of nights requires.
	 * @return void
	 *
	 * @throws \LogicException
	 */
	public function requires_minimum_nights( $nights = 1 ) {
		if ( $this->nights() < $nights ) {
			/* translators: %d: Number of nights */
			throw new \LogicException( sprintf( esc_html__( 'The timespan requires at least %d nights', 'awebooking' ), esc_html( $nights ) ) );
		}
	}
}
