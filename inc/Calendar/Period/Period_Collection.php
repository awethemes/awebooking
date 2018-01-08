<?php
namespace AweBooking\Calendar\Period;

use AweBooking\Support\Collection;

class Period_Collection extends Collection {
	/**
	 * Create period collection.
	 *
	 * @param array $periods An array of periods.
	 * @throws \InvalidArgumentException
	 */
	public function __construct( $periods ) {
		foreach ( $periods = $this->getArrayableItems( $periods ) as $period ) {
			static::assert_is_period( $period );
		}

		$this->items = $periods;
	}

	/**
	 * Collapse the collection of periods into a single period.
	 *
	 * @return Period|null
	 */
	public function collapse() {
		$periods = $this->items;

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
		$abuts = true;
		$periods = $sort ? $this->sort() : $this->items;

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

	/**
	 * {@inheritdoc}
	 */
	public function prepend( $value, $key = null ) {
		static::assert_is_period( $value );

		parent::prepend( $value, $key );
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetSet( $key, $value ) {
		static::assert_is_period( $value );

		parent::offsetSet( $key, $value );
	}

	/**
	 * Assert given value instance of Period.
	 *
	 * @param  mixed $value Input value.
	 * @return void
	 *
	 * @throws \InvalidArgumentException
	 */
	protected static function assert_is_period( $value ) {
		if ( ! $value instanceof Period ) {
			throw new \InvalidArgumentException( 'Must receive a Period object. Received: ' . get_class( $value ) );
		}
	}
}
