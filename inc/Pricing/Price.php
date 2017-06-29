<?php
namespace AweBooking\Pricing;

use InvalidArgumentException;
use AweBooking\Support\Formatting;
use AweBooking\Interfaces\Currency;
use AweBooking\Interfaces\Price as Price_Interface;

class Price implements Price_Interface {
	/**
	 * The price amount.
	 *
	 * @var float
	 */
	protected $amount;

	/**
	 * The price currency.
	 *
	 * @var Currency
	 */
	protected $currency;

	/**
	 * Create Price from amount integer.
	 *
	 * @param  integer  $amount   Price amount as integer.
	 * @param  Currency $currency The pricing currency.
	 * @return static
	 */
	public static function from_amount( $amount, Currency $currency = null ) {
		return new static( Formatting::amount_to_decimal( $amount ), $currency );
	}

	/**
	 * Quick create zero price.
	 *
	 * @param  Currency|null $currency The pricing currency.
	 * @return static
	 */
	public static function zero( Currency $currency = null ) {
		return new static( 0, $currency );
	}

	/**
	 * Creates a price instance.
	 *
	 * TODO: Try using "bcmath" for calculator.
	 *
	 * @param float    $amount   The price amount.
	 * @param Currency $currency The pricing currency.
	 */
	public function __construct( $amount, Currency $currency = null ) {
		if ( is_null( $currency ) ) {
			$currency = awebooking()->make( 'currency' );
		}

		$this->currency = $currency;
		$this->set_amount( $amount );
	}

	/**
	 * Get current price currency.
	 *
	 * @return Currency
	 */
	public function get_currency() {
		return $this->currency;
	}

	/**
	 * Returns the price amount.
	 *
	 * @return float
	 */
	public function get_amount() {
		return $this->amount;
	}

	/**
	 * Set price amount.
	 *
	 * @param float $amount Price amount.
	 * @throws InvalidArgumentException
	 */
	public function set_amount( $amount ) {
		if ( ! is_numeric( $amount ) ) {
			throw new InvalidArgumentException( esc_html__( 'Price amount must be numeric.', 'awebooking' ) );
		}

		$this->amount = (float) $amount;
	}

	/**
	 * Add a price.
	 *
	 * @param  Price $other Other price.
	 * @return Price
	 */
	public function add( Price $other ) {
		$this->assert_same_currency( $this, $other );

		$amount = $this->amount + $other->get_amount();

		return new static( $amount, $this->get_currency() );
	}

	/**
	 * Subtract price.
	 *
	 * @param  Price $other Other price.
	 * @return Price
	 */
	public function subtract( Price $other ) {
		$this->assert_same_currency( $this, $other );

		$amount = $this->get_amount() - $other->get_amount();

		return new static( $amount, $this->get_currency() );
	}

	/**
	 * Multiply Price by the given factor.
	 *
	 * @param  int $factor The factor number.
	 * @return Price
	 */
	public function multiply( $factor ) {
		$amount = $this->get_amount() * $factor;

		return new static( $amount, $this->get_currency() );
	}

	/**
	 * Divide Proce by the given divisor.
	 *
	 * @param  int $divisor Divisor number.
	 * @return Price
	 *
	 * @throws InvalidArgumentException
	 */
	public function divide( $divisor ) {
		if ( 0 == $divisor ) {
			throw new InvalidArgumentException( esc_html__( 'Divisor is 0', 'awebooking' ) );
		}

		$amount = $this->get_amount() / $divisor;

		return new static( $amount, $this->get_currency() );
	}

	/**
	 * Is price same with other price.
	 *
	 * @param  Price $other Other price.
	 * @return boolean
	 */
	public function equals( Price $other ) {
		$this->assert_same_currency( $this, $other );

		return $this->get_amount() === $other->get_amount();
	}

	/**
	 * If zero amount.
	 *
	 * @return boolean
	 */
	public function is_zero() {
		return $this->get_amount() === 0.00;
	}

	/**
	 * Ensures that the two Price instances have the same currency.
	 *
	 * @param Price $a The price A.
	 * @param Price $b The price B.
	 *
	 * @throws CurrencyMismatchException
	 */
	protected function assert_same_currency( Price $a, Price $b ) {
		if ( $a->get_currency() !== $b->get_currency() ) {
			throw new CurrencyMismatchException;
		}
	}

	/**
	 * Get price number as amount (integer).
	 *
	 * @return integer
	 */
	public function to_amount() {
		return Formatting::decimal_to_amount(
			$this->get_amount()
		);
	}

	/**
	 * Output formatted price.
	 *
	 * @return string
	 */
	public function __toString() {
		return Formatting::price_format( $this );
	}
}
