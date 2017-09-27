<?php
namespace AweBooking\Support;

class Period_Collection {
	/**
	 * An array collection of periods.
	 *
	 * @var array
	 */
	protected $periods = [];

	/**
	 * Create period collection.
	 *
	 * @param array $periods An array of periods.
	 * @throws \InvalidArgumentException
	 */
	public function __construct( array $periods ) {
		foreach ( $periods as $period ) {
			if ( ! $period instanceof Period ) {
				throw new \InvalidArgumentException( 'Must receive a Period object. Received: ' . get_class( $period ) );
			}
		}

		$this->periods = $periods;
	}

	/**
	 * Merge periods in to single period.
	 *
	 * @return Period|null
	 */
	public function merge() {
		$periods = $this->periods;

		// Returns null if empty periods.
		if ( 0 === count( $periods ) ) {
			return;
		}

		// If have only one period in list,
		// just return the first period, don't need to merge.
		if ( 1 === count( $periods ) ) {
			return $periods[0];
		}

		$period = array_shift( $periods );
		return call_user_func_array( [ $period, 'merge' ], $periods );
	}

	/**
	 * Sort periods from earliest to latest.
	 *
	 * @return array
	 */
	public function sort() {
		$sort_periods = $this->periods;

		usort($sort_periods, function ( Period $period1, Period $period2 ) {
			if ( $period1->isBefore( $period2 ) ) {
				return -1;
			}

			if ( $period1->isAfter( $period2 ) ) {
				return 1;
			}

			return 0;
		});

		return $sort_periods;
	}

	/**
	 * Alias of `is_continuous` method.
	 *
	 * @param  boolean $sort Sort periods list before check.
	 * @return boolean
	 */
	public function adjacents( $sort = true ) {
		return $this->is_continuous( $sort );
	}

	/**
	 * Tells whether periods is continuous.
	 *
	 * @param  bool $sort Sort periods list before check.
	 * @return bool
	 */
	public function is_continuous( $sort = true ) {
		$abuts = true;
		$periods = $sort ? $this->sort() : $this->periods;

		// Empty periods found, leave and return false.
		if ( 0 === count( $periods ) ) {
			return false;
		}

		$last_period = null;
		foreach ( $periods as $period ) {
			if ( ! is_null( $last_period ) && ! $last_period->abuts( $period ) ) {
				$abuts = false;
				break;
			}

			$last_period = $period;
		}

		return $abuts;
	}
}
