<?php

namespace AweBooking\Calendar\Event\Core;

use AweBooking\Calendar\Event\Event;
use AweBooking\Calendar\Event\With_Only_Days;
use AweBooking\Calendar\Resource\Resource_Interface;

class Pricing_Event extends Event {
	use With_Only_Days;

	/**
	 * Create an event.
	 *
	 * Note: The $amount value will be convert to Decimal object type.
	 *
	 * @param Resource_Interface $resource   The resource implementation.
	 * @param \DateTime|string   $start_date The start date of the event.
	 * @param \DateTime|string   $end_date   The end date of the event.
	 * @param float|int          $amount     The amount represent for this event.
	 *
	 * @throws \LogicException
	 */
	public function __construct( Resource_Interface $resource, $start_date, $end_date, $amount = 0 ) {
		parent::__construct( $resource, $start_date, $end_date, abrs_decimal( $amount ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_value() {
		return $this->value->as_raw_value();
	}

	/**
	 * {@inheritdoc}
	 */
	public function set_value( $value ) {
		$this->value = abrs_decimal( $value );

		return $this;
	}

	/**
	 * Get Decimal amount represent for this event.
	 *
	 * @return \AweBooking\Support\Decimal
	 */
	public function get_amount() {
		return $this->value;
	}

	/**
	 * Apply an operation to adjust current amount.
	 *
	 * @param  float|int $amount    The amount.
	 * @param  string    $operation The operation.
	 *
	 * @return \AweBooking\Support\Decimal
	 */
	public function apply_operation( $amount, $operation ) {
		$amount = abrs_decimal( $amount );

		switch ( $operation ) {
			case 'replace':
				return $this->value = $amount;
			case 'add':
			case 'plus':
				return $this->value = $this->value->add( $amount );
			case 'sub':
			case 'subtract':
				return $this->value = $this->value->subtract( $amount );
			case 'mul':
			case 'multiply':
				return $this->value = $this->value->multiply( $amount );
			case 'div':
			case 'divide':
				return $this->value = ! $amount->is_zero() ? $this->value->divide( $amount ) : abrs_decimal( 0 );
			case 'increase':
				return $this->value = $this->value->add( $this->value->to_percentage( $amount ) );
			case 'decrease':
				return $this->value = $this->value->discount( $amount );
			default:
				return $this->value;
		}
	}
}
