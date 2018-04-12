<?php
namespace AweBooking\Model\Common;

use AweBooking\Support\Decimal;

class Money {
	/**
	 * The amount.
	 *
	 * @var \AweBooking\Support\Decimal
	 */
	protected $amount;

	/**
	 * The currency.
	 *
	 * @var string
	 */
	protected $currency;

	/**
	 * Create a new money by given amount with currency.
	 *
	 * @param  mixed  $amount   The amount.
	 * @param  string $currency Optional, the currency.
	 * @return static
	 */
	public static function of( $amount, $currency = null ) {
		return new static( $amount, $currency );
	}

	/**
	 * Create a zero money.
	 *
	 * @param  string $currency Optional, the currency.
	 * @return static
	 */
	public static function zero( $currency = null ) {
		return new static( 0, $currency );
	}

	/**
	 * Creates a Price instance.
	 *
	 * @param mixed  $amount   The amount.
	 * @param string $currency Optional, the currency.
	 */
	public function __construct( $amount, $currency = null ) {
		$this->amount = Decimal::create( $amount );
		$this->currency = $currency;
	}

	/**
	 * Returns the amount.
	 *
	 * @return \AweBooking\Support\Decimal
	 */
	public function get_amount() {
		return $this->amount;
	}

	/**
	 * Returns the currency.
	 *
	 * @return \AweBooking\Model\Currency
	 */
	public function get_currency() {
		return $this->currency;
	}
}
