<?php
namespace AweBooking\Money;

use AweBooking\Support\Decimal;
use AweBooking\Support\Formatting;
use AweBooking\Support\Contracts\Stringable;

class Money implements Stringable {
	/**
	 * The amount.
	 *
	 * @var \AweBooking\Support\Decimal
	 */
	protected $amount;

	/**
	 * The currency.
	 *
	 * @var \AweBooking\Money\Currency
	 */
	protected $currency;

	/**
	 * The methods that can be proxied.
	 *
	 * @var array
	 */
	protected static $proxies = [
		0 => [ // @codingStandardsIgnoreStart
			'equals', 'not_equals', 'greater_than', 'greater_than_or_equal', 'less_than', 'less_than_or_equal',
			'add', 'subtract', 'sub',  'percentage_of', 'discount_percentage_of',
		],
		1 => [
			'multiply', 'mul', 'divide', 'div', 'is_zero', 'is_positive', 'is_negative',
			'abs', 'to_percentage', 'discount',
		], // @codingStandardsIgnoreEnd
	];

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

		$this->currency = $this->resolve_currency( $currency );
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
	 * @return \AweBooking\Money\Currency
	 */
	public function get_currency() {
		return $this->currency;
	}

	/**
	 * Returns a numeric representation.
	 *
	 * @return int|float
	 */
	public function as_numeric() {
		return $this->amount->as_numeric();
	}

	/**
	 * {@inheritdoc}
	 */
	public function as_string() {
		return Formatting::money( $this );
	}

	/**
	 * Doing resolve the currency.
	 *
	 * @param  mixed $currency The currency instance of code.
	 * @return \AweBooking\Money\Currency
	 */
	protected function resolve_currency( $currency ) {
		$currencies = Currencies::get_instance();

		if ( empty( $currency ) ) {
			return $currencies->get_current();
		}

		if ( $currency instanceof Currency ) {
			return $currency;
		}

		return $currencies->get( $currency );
	}

	/**
	 * Ensures that the two Money have the same currency.
	 *
	 * @param \AweBooking\Money\Money $a The money A.
	 * @param \AweBooking\Money\Money $b The money B.
	 *
	 * @throws \DomainException
	 */
	protected static function assert_same_currency( Money $a, Money $b ) {
		if ( $a->get_currency()->get_code() !== $b->get_currency()->get_code() ) {
			throw new \DomainException( 'Can\'t operate on amounts with different currencies.' );
		}
	}

	/**
	 * Allow echo class as string.
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->as_string();
	}

	/**
	 * Convenience factory method for a Money object.
	 *
	 * <code>
	 * $five_dollar = Money::USD(500);
	 * </code>
	 *
	 * @param string $method     Static call method.
	 * @param array  $parameters Static call method parameters.
	 * @return static
	 */
	public static function __callStatic( $method, $parameters ) {
		return new static( $parameters[0], new Currency( $method ) );
	}

	/**
	 * Proxy call method from Decimal.
	 *
	 * @param  string $method     Call method.
	 * @param  array  $parameters Call method parameters.
	 * @return mixed
	 *
	 * @throws \BadMethodCallException
	 */
	public function __call( $method, $parameters ) {
		if ( ! in_array( $method, array_merge( static::$proxies[0], static::$proxies[1] ) ) ) {
			throw new \BadMethodCallException( "Method [{$method}] does not exist." );
		}

		if ( in_array( $method, static::$proxies[0] ) && isset( $parameters[0] ) ) {
			$other = $parameters[0];

			if ( $other instanceof self ) {
				$this->assert_same_currency( $this, $other );
			} else {
				$other = static::of( $other, $this->currency );
			}

			$parameters[0] = $other->get_amount();
		}

		// Make call the proxy method.
		$result = $this->amount->{$method}( ...$parameters );

		// If any methods return a new instance of Decimal,
		// so we need to return new instance of Money too.
		return ( $result instanceof Decimal ) ? static::of( $result, $this->currency ) : $result;
	}
}
