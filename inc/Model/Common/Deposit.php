<?php
namespace AweBooking\Model\Common;

use AweBooking\Support\Decimal;
use AweBooking\Formatting as F;
use AweBooking\Support\Contracts\Stringable;

class Deposit implements Stringable {
	const FIXED = 'fixed';
	const PERCENTAGE = 'percentage';

	/**
	 * The deposit type (fixed or percentage).
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * The deposit amount.
	 *
	 * @var \AweBooking\Support\Decimal
	 */
	protected $amount;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Support\Decimal $amount The deposit amount.
	 * @param string                      $type   The deposit type, default: 'percentage'.
	 */
	public function __construct( $amount, $type = 'percentage' ) {
		$this->set_type( $type );

		$this->set_amount( $amount );
	}

	/**
	 * Calculate deposit amount by given a total amount.
	 *
	 * @param  \AweBooking\Support\Decimal $total The total amount.
	 * @return \AweBooking\Support\Decimal
	 */
	public function of( $total ) {
		$amount = $this->get_amount();

		if ( static::FIXED === $this->get_type() ) {
			return $amount;
		}

		return Decimal::create( $total )->to_percentage( $amount );
	}

	/**
	 * Get the deposit type.
	 *
	 * @return \AweBooking\Support\Decimal
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Set the deposit type.
	 *
	 * @param string $type The deposit type.
	 */
	public function set_type( $type ) {
		$this->type = ( static::FIXED === strtolower( $type ) ) ? static::FIXED : static::PERCENTAGE;
	}

	/**
	 * Get the amount.
	 *
	 * @return \AweBooking\Support\Decimal
	 */
	public function get_amount() {
		return $this->amount;
	}

	/**
	 * Set the new amount.
	 *
	 * @param \AweBooking\Support\Decimal $amount The deposit amount.
	 */
	public function set_amount( $amount ) {
		$this->amount = Decimal::create( $amount );
	}

	/**
	 * Get the label to display.
	 *
	 * @return string
	 */
	public function get_label() {
		$amount = $this->get_amount();

		if ( static::FIXED === $this->get_type() ) {
			return F::money( $amount, true );
		}

		return F::number( $amount->as_string(), true ) . '%';
	}

	/**
	 * {@inheritdoc}
	 */
	public function to_array() {
		return [
			'type'   => $this->get_type(),
			'amount' => $this->get_amount()->as_numeric(),
			'label'  => $this->get_label(),
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function as_string() {
		return $this->get_label();
	}
}
