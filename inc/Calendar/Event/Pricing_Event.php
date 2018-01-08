<?php
namespace AweBooking\Calendar\Event;

use AweBooking\Support\Decimal;
use AweBooking\Calendar\Resource\Resource_Interface;

class Pricing_Event extends Event {
	/**
	 * Create an event.
	 *
	 * Note: The $amount value will be convert to Decimal object type.
	 *
	 * @param Resource_Interface $resource   The resource implementation.
	 * @param DateTime|string    $start_date The start date of the event.
	 * @param DateTime|string    $end_date   The end date of the event.
	 * @param Decimal|float|int  $amount     The amount represent for this event.
	 *
	 * @throws \LogicException
	 */
	public function __construct( Resource_Interface $resource, $start_date, $end_date, $amount = 0 ) {
		parent::__construct( $resource, $start_date, $end_date, $amount );
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
		$this->value = Decimal::create( $value );

		return $this;
	}

	/**
	 * Get Decimal amount represent for this event.
	 *
	 * @return Decimal
	 */
	public function get_amount() {
		return $this->value;
	}
}
