<?php
namespace AweBooking\Pricing;

use InvalidArgumentException;
use AweBooking\Currency\Currency;
use AweBooking\Support\Formatting;

class Price {
	/**
	 * The scale used in BCMath calculations.
	 */
	const BCMATH_SCALE = 6;

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
	 * Creates a Price instance.
	 *
	 * @param float    $amount   The price amount.
	 * @param Currency $currency Optinal, the currency if null given default currency will be used.
	 */
	public function __construct( $amount, Currency $currency = null ) {
		$this->set_amount( $amount );
		$this->currency = $currency ?: awebooking()->make( 'currency' );
	}

	/**
	 * Quick create zero price, current currency.
	 *
	 * @return static
	 */
	public static function zero() {
		return new static( 0 );
	}

	/**
	 * Create a Price from integer amount.
	 *
	 * Note: 1999 same as 19.99, depend on `price_number_decimals` setting.
	 *
	 * @param  integer $amount Price amount as integer.
	 * @return static
	 */
	public static function from_integer( $amount ) {
		return new static( Formatting::amount_to_decimal( $amount ) );
	}

	/**
	 * Convenience factory method for a Price object.
	 *
	 * <code>
	 * $fiveDollar = Money::USD(500);
	 * </code>
	 *
	 * @param string $method    Static call method.
	 * @param array  $arguments Static call method arguments.
	 * @return static
	 */
	public static function __callStatic( $method, $arguments ) {
		return new static( $arguments[0], new Currency( $method ) );
	}

	/**
	 * Returns a new Price representing the sum of this Price and another.
	 *
	 * @param  Price $other Other price.
	 * @return $this
	 */
	public function add( Price $other ) {
		$this->assert_same_currency( $this, $other );

		if ( function_exists( 'bcadd' ) ) {
			$amount = bcadd( $this->get_amount(), $other->get_amount(), static::BCMATH_SCALE );
		} else {
			$amount = $this->get_amount() + $other->get_amount();
		}

		return $this->new_price( $amount );
	}

	/**
	 * Returns a new Price representing the difference of this Price and another.
	 *
	 * @param  Price $other Other price.
	 * @return Price
	 */
	public function subtract( Price $other ) {
		$this->assert_same_currency( $this, $other );

		if ( function_exists( 'bcsub' ) ) {
			$amount = bcsub( $this->get_amount(), $other->get_amount(), static::BCMATH_SCALE );
		} else {
			$amount = $this->get_amount() - $other->get_amount();
		}

		return $this->new_price( $amount );
	}

	/**
	 * Returns a new Price representing the value of this Price multiplied.
	 *
	 * @param  int $factor The factor number.
	 * @return Price
	 */
	public function multiply( $factor ) {
		$this->assert_numeric( $factor );

		if ( function_exists( 'bcmul' ) ) {
			$amount = bcmul( $this->get_amount(), $factor, static::BCMATH_SCALE );
		} else {
			$amount = $this->get_amount() * $factor;
		}

		return $this->new_price( $amount );
	}

	/**
	 * Returns a new Price representing the value of this Price divided
	 * by the given divisor.
	 *
	 * @param  int $divisor Divisor number.
	 * @return Price
	 *
	 * @throws InvalidArgumentException
	 */
	public function divide( $divisor ) {
		$this->assert_numeric( $divisor );

		if ( 0 == $divisor ) {
			throw new InvalidArgumentException( 'Divisor cannot be zero' );
		}

		if ( function_exists( 'bcdiv' ) ) {
			$amount = bcdiv( $this->get_amount(), $divisor, static::BCMATH_SCALE );
		} else {
			$amount = $this->get_amount() / $divisor;
		}

		return $this->new_price( $amount );
	}

	/**
	 * Converts the currency of this Price object to
	 * a given target currency with a given conversion rate
	 *
	 * @param Currency $currency Conver to Currency.
	 * @param numeric  $rate     Rate amount.
	 *
	 * @return Price
	 */
	public function convert( Currency $currency, $rate = 1 ) {
		$this->assert_numeric( $rate );

		if ( function_exists( 'bcmul' ) ) {
			$amount = bcmul( $this->get_amount(), $rate, static::BCMATH_SCALE );
		} else {
			$amount = $this->get_amount() * $rate;
		}

		return new static( $amount, $currency );
	}

	/**
	 * Returns `true` if this Price equals another.
	 *
	 * @param  Price $other Other price.
	 * @return bool
	 */
	public function equals( Price $other ) {
		return $this->compare_to( $other ) === 0;
	}

	/**
	 * Returns `true` if this Price is greater than another.
	 *
	 * @param  Price $other Other price.
	 * @return bool
	 */
	public function greater_than( Price $other ) {
		return $this->compare_to( $other ) === 1;
	}

	/**
	 * Returns `true` if this Price is greater than or equal to another.
	 *
	 * @param  Price $other Other price.
	 * @return bool
	 */
	public function greater_than_or_equal( Price $other ) {
		return $this->greater_than( $other ) || $this->equals( $other );
	}

	/**
	 * Returns `true` if this Price is smaller than another.
	 *
	 * @param  Price $other Other price.
	 * @return bool
	 */
	public function less_than( Price $other ) {
		return $this->compare_to( $other ) === -1;
	}

	/**
	 * Returns `true` if this Price is smaller than or equal to another.
	 *
	 * @param  Price $other Other price.
	 * @return bool
	 */
	public function less_than_or_equal( Price $other ) {
		return $this->less_than( $other ) || $this->equals( $other );
	}

	/**
	 * Checks if the value represented by this Price is zero.
	 *
	 * @return bool
	 */
	public function is_zero() {
		return $this->compare_to_zero() === 0;
	}

	/**
	 * Checks if the value represented by this Price is positive.
	 *
	 * @return boolean
	 */
	public function is_positive() {
		return $this->compare_to_zero() === 1;
	}
	/**
	 * Checks if the value represented by this Price is negative.
	 *
	 * @return boolean
	 */
	public function is_negative() {
		return $this->compare_to_zero() === -1;
	}

	/**
	 * Set price amount.
	 *
	 * @param float $amount Price amount.
	 * @throws InvalidArgumentException
	 */
	public function set_amount( $amount ) {
		$this->assert_numeric( $amount );

		$this->amount = $amount;

		return $this;
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
	 * Returns the price currency.
	 *
	 * @return Currency
	 */
	public function get_currency() {
		return $this->currency;
	}

	/**
	 * Get price number as amount (integer).
	 *
	 * @return int
	 */
	public function to_integer() {
		return Formatting::decimal_to_amount( $this->get_amount() );
	}

	/**
	 * Output formatted price.
	 *
	 * @return string
	 */
	public function __toString() {
		return Formatting::price_format( $this );
	}

	/**
	 * Create new price with new amount and same currency.
	 *
	 * @param  string|float $amount New price amount.
	 * @return static
	 */
	protected function new_price( $amount ) {
		$amount = Formatting::format_decimal( $amount );

		return new static( $amount, $this->get_currency() );
	}

	/**
	 * Compares this Price to another.
	 *
	 * Returns an integer less than, equal to, or greater than zero
	 * if the value of this Price is considered to be respectively
	 * less than, equal to, or greater than the other Price.
	 *
	 * @param  Price $other Other price to compare.
	 * @return integer      -1|0|1
	 */
	protected function compare_to( Price $other ) {
		$this->assert_same_currency( $this, $other );

		if ( function_exists( 'bccomp' ) ) {
			return bccomp( $this->get_amount(), $other->get_amount(), static::BCMATH_SCALE );
		}

		return $this->get_amount() == $other->get_amount() ? 0 :
			( $this->get_amount() < $other->get_amount() ? -1 : 1 );
	}

	/**
	 * Compares this Price to zero.
	 *
	 * Returns an integer less than, equal to, or greater than zero
	 * if the value of this object is considered to be respectively
	 * less than, equal to, or greater than 0.
	 *
	 * @return integer -1|0|1
	 */
	protected function compare_to_zero() {
		if ( function_exists( 'bccomp' ) ) {
			return bccomp( $this->amount, '', static::BCMATH_SCALE );
		}

		return $this->get_amount() == 0 ? 0 :
			( $this->get_amount() > 0 ? 1 : -1 );
	}

	/**
	 * Ensures that the two Price instances have the same currency.
	 *
	 * @param Price $a The price A.
	 * @param Price $b The price B.
	 *
	 * @throws Currency_Mismatch_Exception
	 */
	protected function assert_same_currency( Price $a, Price $b ) {
		if ( ! $a->get_currency()->equals( $b->get_currency() ) ) {
			throw new Currency_Mismatch_Exception;
		}
	}

	/**
	 * Asserts that a value is a valid numeric string
	 *
	 * @param numeric $value Testing value.
	 *
	 * @throws InvalidArgumentException If $value is not numeric.
	 */
	protected static function assert_numeric( $value ) {
		if ( ! is_numeric( $value ) ) {
			throw new InvalidArgumentException( 'Amount must be a valid numeric value' );
		}
	}
}
