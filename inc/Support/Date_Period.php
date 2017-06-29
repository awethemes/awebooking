<?php
namespace AweBooking\Support;

use DatePeriod;
use DateInterval;
use Carbon\Carbon;
use LogicException;

class Date_Period implements \IteratorAggregate {
	/* Constants */
	const EXCLUDE_START_DATE = 1;
	const EXCLUDE_END_DATE = 2;

	/**
	 * Date period instance.
	 *
	 * @var DatePeriod
	 */
	protected $period;

	/**
	 * List of day in the period.
	 *
	 * @var array Carbon[]
	 */
	protected $periods = [];

	/**
	 * Date period options.
	 *
	 * @var integer
	 */
	protected $options;

	/**
	 * Start date instance.
	 *
	 * @var Carbon
	 */
	protected $start_date;

	/**
	 * End date instance.
	 *
	 * @var Carbon
	 */
	protected $end_date;

	/**
	 * Create date period.
	 *
	 * The datetime must be a string using
	 * ISO-8601 "Y-m-d" date format, eg: 2017-05-10.
	 *
	 * @param string|Carbon $start_date Start of the time period.
	 * @param string|Carbon $end_date   End of the time period.
	 * @param boolean       $strict     //.
	 * @param int           $options    //.
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct( $start_date, $end_date, $strict = true, $options = 0 ) {
		$this->start_date = Date_Utils::create_date( $start_date );
		$this->end_date   = Date_Utils::create_date( $end_date );

		$this->options = $options;
		$this->validate_the_date( $this->start_date, $this->end_date, $strict );

		$interval = new DateInterval( 'P1D' );
		$this->period = new DatePeriod( $this->start_date, $interval, $this->end_date, DatePeriod::EXCLUDE_START_DATE );
	}

	/**
	 * Get the start date.
	 *
	 * @return Carbon
	 */
	public function get_start_date() {
		return Carbon::instance( $this->start_date );
	}

	/**
	 * Get the end date.
	 *
	 * @return Carbon
	 */
	public function get_end_date() {
		return Carbon::instance( $this->end_date );
	}

	/**
	 * Get number of nights.
	 *
	 * @return integer
	 */
	public function nights() {
		return $this->get_end_date()->diffInDays(
			$this->get_start_date()
		);
	}

	/**
	 * Retrieve an external iterator.
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator() {
		return new \ArrayIterator( $this->get_periods() );
	}

	/**
	 * Get datetime in the period.
	 *
	 * @return array Carbon[]
	 */
	public function get_periods() {
		if ( ! $this->periods ) {
			if ( 0 === ( $this->options & static::EXCLUDE_START_DATE ) ) {
				$this->periods[] = $this->get_start_date();
			}

			foreach ( $this->period as $datetime ) {
				$this->periods[] = Carbon::instance( $datetime );
			}

			if ( 0 === ( $this->options & static::EXCLUDE_END_DATE ) ) {
				$this->periods[] = $this->get_end_date();
			}
		}

		return $this->periods;
	}

	/**
	 * Validate the period.
	 *
	 * @param string|Carbon $start_date Start of the time period.
	 * @param string|Carbon $end_date   End of the time period.
	 * @param boolean       $strict     Using strict mode.
	 *
	 * @throws LogicException
	 */
	protected function validate_the_date( Carbon $start_date, Carbon $end_date, $strict ) {
		if ( $start_date->gt( $end_date ) ) {
			throw new LogicException( esc_html__( 'The period is invalid.', 'awebooking' ) );
		}

		// Required minimum one night for the period.
		if ( $strict && $start_date->isSameDay( $end_date ) ) {
			throw new LogicException( esc_html__( 'Start-date and end-date cannot on the same day.', 'awebooking' ) );
		}

		// Using strict mode for select past days.
		if ( $strict && $start_date->lt( Carbon::today()->startOfDay() ) ) {
			throw new LogicException( esc_html__( 'Past day exception.', 'awebooking' ) );
		}
	}
}
