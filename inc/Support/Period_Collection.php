<?php

namespace AweBooking\Support;

class Period_Collection extends Collection {
	/**
	 * Collapse the collection of periods into a single period.
	 *
	 * @return Period|null
	 */
	public function collapse() {
		$periods = $this->items;

		// Returns null if empty periods.
		if ( 0 === count( $periods ) ) {
			return null;
		}

		// If have only one period in list,
		// just return the first period, don't need to merge.
		if ( 1 === count( $periods ) ) {
			return $periods[0];
		}

		// Remove first period from $periods and return it.
		$period = array_shift( $periods );

		return $period->merge( ...$periods );
	}

	/**
	 * Sort periods from earliest to latest.
	 *
	 * @param  callable|null $callback Optional, callback user-defined comparison function.
	 * @return static
	 */
	public function sort( callable $callback = null ) {
		$sort_periods = $this->items;

		$callback
			? usort( $sort_periods, $callback )
			: usort( $sort_periods, function ( Period $period1, Period $period2 ) {
				if ( $period1->isBefore( $period2 ) ) {
					return -1;
				}

				if ( $period1->isAfter( $period2 ) ) {
					return 1;
				}

				return 0;
			});

		return new static( $sort_periods );
	}

	/**
	 * Tells whether periods is continuous.
	 *
	 * @param  bool $sort Sort periods list before check.
	 * @return bool
	 */
	public function is_continuous( $sort = true ) {
		// Empty periods, leave and return false.
		if ( 0 === count( $this->items ) ) {
			return false;
		}

		// In case we have only one item, it's true.
		if ( 1 === count( $this->items ) ) {
			return true;
		}

		$periods = $sort ? $this->sort() : new static( $this->items );

		$periods = $periods->unique( function( Period $period ) {
			return $period->get_start_date()->format( 'Y-m-d' ) . '::' . $period->get_end_date()->format( 'Y-m-d' );
		});

		$abuts = true;
		$last_period = null;

		foreach ( $periods as $period ) {
			/* @var \AweBooking\Support\Period $last_period */
			if ( ! is_null( $last_period ) && ! $last_period->abuts( $period ) ) {
				$abuts = false;
				break;
			}

			$last_period = $period;
		}

		return $abuts;
	}
}
