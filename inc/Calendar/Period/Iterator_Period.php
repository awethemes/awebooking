<?php

namespace AweBooking\Calendar\Period;

use DateInterval;
use IteratorAggregate;
use AweBooking\Support\Period;

class Iterator_Period extends Period implements IteratorAggregate {
	/**
	 * The date interval specification for the period.
	 *
	 * @var string
	 */
	protected $interval = 'P1D';

	/**
	 * {@inheritdoc}
	 */
	public function getIterator() {
		// @codingStandardsIgnoreLine
		$initial = new Day( $this->get_start_date() );

		return $this->generate_iterator( $initial, function( $current, $initial ) {
			/* @var \AweBooking\Calendar\Period\Iterator_Period $current */
			return $this->contains( $current->get_start_date() );
		});
	}

	/**
	 * The the DateInterval represent for period.
	 *
	 * @return \DateInterval
	 */
	protected function get_interval() {
		return new DateInterval( $this->interval );
	}

	/**
	 * Generate the iterator.
	 *
	 * @param  Period   $initial  The initial value.
	 * @param  callable $callback The callback, must be returned "false" sometimes for stop the while loop.
	 * @return \Generator
	 */
	protected function generate_iterator( Period $initial, callable $callback ) {
		$current = $initial;

		while ( $callback( $current, $initial ) ) {
			yield (string) $current => $current;

			/* @var \AweBooking\Calendar\Period\Iterator_Period $current */
			$current = $current->next( $current->get_interval() );
		}
	}

	/**
	 * Given a datepoint, then return an array of period.
	 *
	 * @param \DateTime|string $start_date The start date point.
	 * @return array
	 */
	protected function filter_from_datepoint( $start_date ) {
		$start_date = abrs_date_time( $start_date );

		$interval = static::filterDateInterval( $this->get_interval() );

		return [ $start_date, $start_date->copy()->add( $interval ) ];
	}
}
