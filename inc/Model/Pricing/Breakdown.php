<?php

namespace AweBooking\Model\Pricing;

use AweBooking\Constants;
use AweBooking\Support\Decimal;
use AweBooking\Model\Common\Timespan;
use Illuminate\Support\Arr;

class Breakdown implements \ArrayAccess, \IteratorAggregate {
	/**
	 * The timespan for the breakdown.
	 *
	 * @var \AweBooking\Model\Common\Timespan
	 */
	protected $timespan;

	/**
	 * The rack rate (price per day/night).
	 *
	 * @var float|int
	 */
	protected $rack_rate;

	/**
	 * The granularity: daily or nightly.
	 *
	 * @var string
	 */
	protected $granularity;

	/**
	 * The breakdown data.
	 *
	 * @var array
	 */
	protected $data = [];

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Model\Common\Timespan $timespan    The timespan.
	 * @param int|float                         $rack_rate   The rack rate.
	 * @param string                            $granularity The granularity.
	 */
	public function __construct( Timespan $timespan, $rack_rate, $granularity = Constants::GL_NIGHTLY ) {
		$this->timespan    = $timespan;
		$this->rack_rate   = $rack_rate;
		$this->granularity = $granularity;
	}

	/**
	 * Get a specified date amount.
	 *
	 * @param \DateTimeInterface|string $date The date (must included in the period).
	 *
	 * @return float|null
	 */
	public function get( $date ) {
		$key = $this->format_index_key( $date );

		$breakdown = $this->get_breakdown();

		return array_key_exists( $key, $breakdown ) ? $breakdown[ $key ] : null;
	}

	/**
	 * Sets a specified date value in the breakdown data.
	 *
	 * @param \DateTimeInterface|string             $date   The date (must included in the period).
	 * @param int|float|\AweBooking\Support\Decimal $amount The amount.
	 *
	 * @return bool
	 */
	public function set( $date, $amount ) {
		$index = $this->format_index_key( $date );

		$period = $this->timespan
			->to_period( $this->granularity );

		if ( $period->contains( abrs_date( $index ) ) ) {
			$this->data[ $index ] = $this->format_amount( $amount );

			return true;
		}

		return false;
	}

	/**
	 * Merge the breakdown data with a itemized.
	 *
	 * @param array|\AweBooking\Support\Collection $itemized The itemized data.
	 *
	 * @return $this
	 */
	public function merge( $itemized ) {
		foreach ( $itemized as $key => $value ) {
			$this->set( $key, $value );
		}

		return $this;
	}

	/**
	 * Get the sum value.
	 *
	 * @return float
	 */
	public function sum() {
		$breakdown = $this->get_breakdown();

		if ( 0 === count( $breakdown ) ) {
			return 0;
		}

		/* @var $sum \AweBooking\Support\Decimal */
		$sum = array_reduce( $breakdown, function ( Decimal $total, $amount ) {
			return $total->add( $amount );
		}, Decimal::zero() );

		return $sum->as_numeric();
	}

	/**
	 * Get the average value.
	 *
	 * @return float|null
	 */
	public function avg() {
		$breakdown = $this->get_breakdown();

		if ( ! $count = count( $breakdown ) ) {
			return null;
		}

		return abrs_decimal( $this->sum() )
			->divide( $count )
			->as_numeric();
	}

	/**
	 * Alias for the "avg" method.
	 *
	 * @return mixed
	 */
	public function average() {
		return $this->avg();
	}

	/**
	 * Alias for the "get_breakdown" method.
	 *
	 * @return array
	 */
	public function all() {
		return $this->get_breakdown();
	}

	/**
	 * Return the first breakdown value.
	 *
	 * @return mixed
	 */
	public function first() {
		return Arr::first( $this->get_breakdown() );
	}

	/**
	 * Gets the breakdown data.
	 *
	 * @return array
	 */
	public function get_breakdown() {
		if ( 0 === $this->timespan->nights() ) {
			return [];
		}

		$period = $this->timespan
			->to_period( $this->granularity )
			->getDatePeriod( '1 DAY' );

		// Add missing data.
		foreach ( $period as $item ) {
			$index = $this->format_index_key( $item );

			if ( ! array_key_exists( $index, $this->data ) ) {
				$this->data[ $index ] = $this->rack_rate;
			}
		}

		return $this->data;
	}

	/**
	 * Format the float amount.
	 *
	 * @param  mixed $amount The amount.
	 * @return float
	 */
	protected function format_amount( $amount ) {
		if ( $amount instanceof Decimal ) {
			return $amount->as_numeric();
		}

		if ( ! is_float( $amount ) || ! is_int( $amount ) ) {
			return abrs_sanitize_decimal( $amount );
		}

		return $amount;
	}

	/**
	 * Format the given date to "Y-m-d".
	 *
	 * @param \DateTimeInterface|string $date The date.
	 *
	 * @return string
	 */
	protected function format_index_key( $date ) {
		if ( abrs_is_standard_date( $date ) ) {
			return $date;
		}

		return $date->format( 'Y-m-d' );
	}

	/**
	 * Get an iterator for the data.
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator() {
		return new \ArrayIterator( $this->get_breakdown() );
	}

	/**
	 * Determine if an item exists at an offset.
	 *
	 * @param  string $key The offset key.
	 * @return bool
	 */
	public function offsetExists( $key ) {
		return array_key_exists( $key, $this->data );
	}

	/**
	 * Get an item at a given offset.
	 *
	 * @param  string $key The offset key.
	 * @return mixed
	 */
	public function offsetGet( $key ) {
		return $this->get( $key );
	}

	/**
	 * Set the item at a given offset.
	 *
	 * @param  string $key   The offset key.
	 * @param  mixed  $value The offset value.
	 * @return void
	 */
	public function offsetSet( $key, $value ) {
		$this->set( $key, $value );
	}

	/**
	 * Unset the item at a given offset.
	 *
	 * @param  string $key The offset key.
	 * @return void
	 */
	public function offsetUnset( $key ) {
		// ...
	}
}
