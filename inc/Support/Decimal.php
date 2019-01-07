<?php

namespace AweBooking\Support;

/**
 * This class adapted from pimcore/pimcore distributed under GPLv3 license.
 *
 * @see https://github.com/pimcore/pimcore/blob/v5.0.2/pimcore/lib/Pimcore/Bundle/EcommerceFrameworkBundle/Type/Decimal.php
 */
class Decimal {
	/* Constants */
	const INTEGER_NUMBER_REGEXP = '/^([+\-]?)\d+$/';
	const DECIMAL_NUMBER_REGEXP = '/^([+\-]?)(\d+)\.(\d+)$/';

	/**
	 * The amount.
	 *
	 * @var int
	 */
	protected $amount;

	/**
	 * The scale (precision after comma).
	 *
	 * Actual amount will be amount divided by 10^scale.
	 *
	 * @var int
	 */
	protected $scale;

	/**
	 * The default scale.
	 *
	 * @var int
	 */
	protected static $default_scale = 4;

	/**
	 * Builds a value from an integer.
	 *
	 * The integer amount here must be the final value with
	 * conversion factor already applied.
	 *
	 * @param int $amount The integer amount.
	 * @param int $scale  The scale.
	 */
	protected function __construct( $amount, $scale ) {
		if ( false === filter_var( $amount, FILTER_VALIDATE_INT ) ) {
			trigger_error( 'The amount should be an integer value', E_USER_WARNING ); // @codingStandardsIgnoreLine
		}

		$this->amount = (int) $amount;
		$this->scale  = (int) $scale;
	}

	/**
	 * Sets the global default scale to be used.
	 *
	 * @param int $scale The scale.
	 */
	public static function set_default_scale( $scale ) {
		$scale = (int) $scale;

		static::validate_scale( $scale );

		static::$default_scale = $scale;
	}

	/**
	 * Validates scale not being negative.
	 *
	 * @param  int $scale The scale.
	 * @throws \DomainException
	 */
	protected static function validate_scale( $scale ) {
		if ( $scale < 0 ) {
			throw new \DomainException( 'Scale must be greater or equal than 0' );
		}
	}

	/**
	 * Asserts that an integer value didn't become something else
	 * (after some arithmetic operation).
	 *
	 * @param int $amount The test amount value.
	 *
	 * @throws \OverflowException  If integer overflow occured.
	 * @throws \UnderflowException If integer underflow occured.
	 */
	protected static function validate_integer_bounds( $amount ) {
		if ( $amount > ( PHP_INT_MAX - 1 ) ) {
			throw new \OverflowException( 'The maximum allowed integer (PHP_INT_MAX) was reached' );
		}

		if ( $amount < ( ~PHP_INT_MAX + 1 ) ) {
			throw new \UnderflowException( 'The minimum allowed integer (PHP_INT_MAX) was reached' );
		}
	}

	/**
	 * Try round value to int value if needed.
	 *
	 * @param string|float|int $value        The input value.
	 * @param int|null         $rounding_mode See round() http://php.net/manual/en/function.round.php.
	 * @return int
	 */
	protected static function to_int_value( $value, $rounding_mode = null ) {
		if ( ! is_int( $value ) ) {
			$value = round( $value, 0, is_null( $rounding_mode ) ? PHP_ROUND_HALF_EVEN : $rounding_mode );
			$value = (int) $value;
		}

		return $value;
	}

	/**
	 * Creates a decimal value.
	 *
	 * If an integer is passed, its value will be used without any conversions. Any
	 * other value (float, string) will be converted to int with the given scale. If a Decimal is
	 * passed, it will be converted to the given scale if necessary. Example:
	 *
	 * ```
	 * input: 15
	 * scale: 4
	 * amount: 15 * 10^4 = 150000, scale: 4
	 * ```
	 *
	 * @param  int|float|string|self $amount        The amount.
	 * @param  int|null              $scale         Optional, custom scale.
	 * @param  int|null              $rounding_mode The rounding mode.
	 * @return static
	 *
	 * @throws \InvalidArgumentException
	 */
	public static function create( $amount, $scale = null, $rounding_mode = null ) {
		if ( is_string( $amount ) ) {
			return static::from_string( $amount, $scale, $rounding_mode );
		}

		if ( is_numeric( $amount ) ) {
			return static::from_numeric( $amount, $scale, $rounding_mode );
		}

		if ( $amount instanceof self ) {
			return static::from_decimal( $amount, $scale );
		}

		throw new \InvalidArgumentException( 'Expected (int, float, string, self), but received ' . ( is_object( $amount ) ? get_class( $amount ) : gettype( $amount ) ) );
	}

	/**
	 * Creates a value from a raw integer input.
	 *
	 * No value conversions will be done.
	 *
	 * @param  int      $amount The amount.
	 * @param  int|null $scale  Optional, the scale.
	 * @return static
	 */
	public static function from_raw_value( $amount, $scale = null ) {
		$scale = ! is_null( $scale ) ? $scale : static::$default_scale;

		static::validate_scale( $scale );

		return new static( $amount, $scale );
	}

	/**
	 * Creates a value from a string input.
	 *
	 * If possible, the integer will be created with string operations
	 * (e.g. adding zeroes), otherwise it will fall back to from_numeric().
	 *
	 * @param  string|float|int $amount        The amount value.
	 * @param  int|null         $scale         Optional, custom default scale.
	 * @param  int|null         $rounding_mode Rounding mode.
	 *
	 * @return static
	 */
	public static function from_string( $amount, $scale = null, $rounding_mode = null ) {
		$scale = ! is_null( $scale ) ? $scale : static::$default_scale;

		static::validate_scale( $scale );

		$result = null;

		if ( 1 === preg_match( static::INTEGER_NUMBER_REGEXP, $amount, $captures ) ) {
			// No decimals -> add zeroes until we have the expected amount
			// e.g. 1234, scale 4 = 12340000.
			$result = (int) ( $amount . str_repeat( '0', $scale ) );
		} elseif ( 1 === preg_match( static::DECIMAL_NUMBER_REGEXP, $amount, $captures ) ) {
			// Decimal part is lower/equals than scale - add zeroes as needed and concat it with the integer part
			// e.g. 123.45 at scale 4 -> 123 (integer) . 4500 (zero padded decimal part) => 1234500.
			if ( strlen( $captures[3] ) <= $scale ) {
				$fractional_part = str_pad( $captures[3], $scale, '0', STR_PAD_RIGHT );
				$result          = (int) ( $captures[1] . $captures[2] . $fractional_part );
			}
		}

		if ( null !== $result ) {
			static::validate_integer_bounds( $result );

			return new static( $result, $scale );
		}

		// Default to numeric - this will also apply rounding as we
		// fall back to floats here.
		return static::from_numeric( $amount, $scale, $rounding_mode );
	}

	/**
	 * Creates a value from a numeric input.
	 *
	 * The given amount will be converted to int with the given scale.
	 * Please note that this implicitely rounds the amount to the
	 * next integer, so precision depends on the given scale.
	 *
	 * @param  string|float|int $amount        The amount value.
	 * @param  int|null         $scale         Optional, custom default scale.
	 * @param  int|null         $rounding_mode Rounding mode.
	 * @return static
	 *
	 * @throws \InvalidArgumentException
	 */
	public static function from_numeric( $amount, $scale = null, $rounding_mode = null ) {
		if ( ! is_numeric( $amount ) ) {
			throw new \InvalidArgumentException( 'The amount must be a numeric value' );
		}

		$scale = ! is_null( $scale ) ? $scale : static::$default_scale;
		static::validate_scale( $scale );

		$result = $amount * ( 10 ** $scale );
		static::validate_integer_bounds( $result );

		$result = static::to_int_value( $result, $rounding_mode );

		return new static( $result, $scale );
	}

	/**
	 * Creates a value from another price value.
	 *
	 * If the scale matches the given scale, the input value will be returned,
	 * otherwise the scale will be converted and a new object will be returned.
	 * Please note that this will potentially imply precision loss when converting to a lower scale.
	 *
	 * @param  Decimal  $amount The Decimal acount.
	 * @param  int|null $scale  The scale value.
	 * @return static
	 */
	public static function from_decimal( Decimal $amount, $scale = null ) {
		$scale = ! is_null( $scale ) ? $scale : static::$default_scale;

		static::validate_scale( $scale );

		// Object is identical - creating a new object is not necessary.
		if ( $amount->get_scale() === $scale ) {
			return $amount;
		}

		return $amount->with_scale( $scale );
	}

	/**
	 * Create a zero value object.
	 *
	 * @param  int|null $scale Optional, custom default scale.
	 * @return static
	 */
	public static function zero( $scale = null ) {
		return static::from_raw_value( 0, $scale );
	}

	/**
	 * Returns the used scale factor.
	 *
	 * @return int
	 */
	public function get_scale() {
		return $this->scale;
	}

	/**
	 * Returns the internal representation value.
	 *
	 * WARNING: use this with caution as the represented value depends on the scale!
	 *
	 * @return int
	 */
	public function as_raw_value() {
		return $this->amount;
	}

	/**
	 * Returns a numeric representation.
	 *
	 * @return int|float
	 */
	public function as_numeric() {
		return $this->amount / ( 10 ** $this->scale );
	}

	/**
	 * Returns a string representation.
	 *
	 * Digits default to the scale. If $digits is passed, the string will be
	 * truncated to the given amount of digits without any rounding.
	 *
	 * @param  int|null $digits The number of digits.
	 * @return string
	 */
	public function as_string( $digits = null ) {
		$signum = $this->amount < 0 ? '-' : '';

		$string = (string) abs( $this->amount );
		$amount = null;

		if ( 0 === $this->scale ) {
			$amount = $string;
		} elseif ( strlen( $string ) <= $this->scale ) {
			$fractional_part = str_pad( $string, $this->scale, '0', STR_PAD_LEFT );
			$amount          = '0.' . $fractional_part;
		} else {
			$fractional_offset = strlen( $string ) - $this->scale;
			$integer_part      = substr( $string, 0, $fractional_offset );
			$fractional_part   = substr( $string, $fractional_offset );
			$amount            = $integer_part . '.' . $fractional_part;
		}

		if ( null !== $digits ) {
			$amount = $this->truncate_decimal_string( $amount, $digits );
		}

		return $signum . $amount;
	}

	/**
	 * Converts decimal string to the given amount of digits.
	 *
	 * No rounding is done here - additional digits are just truncated.
	 *
	 * @param  string $amount The string amount.
	 * @param  int    $digits The number of digits.
	 * @return string
	 */
	protected function truncate_decimal_string( $amount, $digits ) {
		$integer_part    = $amount;
		$fractional_part = '0';

		if ( false !== strpos( $amount, '.' ) ) {
			list( $integer_part, $fractional_part ) = explode( '.', $amount );
		}

		if ( 0 === $digits ) {
			return $integer_part;
		}

		if ( strlen( $fractional_part ) > $digits ) {
			$fractional_part = substr( $fractional_part, 0, $digits );
		} elseif ( strlen( $fractional_part ) < $digits ) {
			$fractional_part = str_pad( $fractional_part, $digits, '0', STR_PAD_RIGHT );
		}

		return $integer_part . '.' . $fractional_part;
	}

	/**
	 * Builds a value with the given scale.
	 *
	 * @param  int      $scale         The scale value.
	 * @param  int|null $rounding_mode The rounding mode.
	 * @return static
	 */
	public function with_scale( $scale, $rounding_mode = null ) {
		static::validate_scale( $scale );

		// No need to create a new object as output would be identical.
		if ( $scale === $this->scale ) {
			return $this;
		}

		$diff = $scale - $this->scale;

		$result = $this->amount * ( 10 ** $diff );
		static::validate_integer_bounds( $result );

		$result = static::to_int_value( $result, $rounding_mode );

		return new static( $result, $scale );
	}

	/**
	 * Checks if value is equal to other value.
	 *
	 * @param  Decimal $other Other Decimal value.
	 * @return bool
	 */
	public function equals( Decimal $other ) {
		return $other->scale === $this->scale && $other->amount === $this->amount;
	}

	/**
	 * Checks if value is not equal to other value.
	 *
	 * @param  Decimal $other Other Decimal value.
	 * @return bool
	 */
	public function not_equals( Decimal $other ) {
		return ! $this->equals( $other );
	}

	/**
	 * Compares a value to another one.
	 *
	 * @param  Decimal $other Other Decimal value.
	 * @return int (0, 1, -1)
	 */
	public function compare( Decimal $other ) {
		$this->assert_same_scale( $other, 'Can\'t compare values with different scales. Please convert both values to the same scale.' );

		if ( $this->amount === $other->amount ) {
			return 0;
		}

		return ( $this->amount > $other->amount ) ? 1 : -1;
	}

	/**
	 * Compares this > other.
	 *
	 * @param  Decimal $other Other Decimal value.
	 * @return bool
	 */
	public function greater_than( Decimal $other ) {
		return $this->compare( $other ) === 1;
	}

	/**
	 * Compares this >= other.
	 *
	 * @param  Decimal $other Other Decimal value.
	 * @return bool
	 */
	public function greater_than_or_equal( Decimal $other ) {
		return $this->compare( $other ) >= 0;
	}

	/**
	 * Compares this < other.
	 *
	 * @param  Decimal $other Other Decimal value.
	 * @return bool
	 */
	public function less_than( Decimal $other ) {
		return $this->compare( $other ) === -1;
	}

	/**
	 * Compares this <= other.
	 *
	 * @param  Decimal $other Other Decimal value.
	 * @return bool
	 */
	public function less_than_or_equal( Decimal $other ) {
		return $this->compare( $other ) <= 0;
	}

	/**
	 * Checks if amount is zero.
	 *
	 * @return bool
	 */
	public function is_zero() {
		return 0 === $this->amount;
	}

	/**
	 * Checks if amount is positive. Not: zero is NOT handled as positive.
	 *
	 * @return bool
	 */
	public function is_positive() {
		return $this->amount > 0;
	}

	/**
	 * Checks if amount is negative.
	 *
	 * @return bool
	 */
	public function is_negative() {
		return $this->amount < 0;
	}

	/**
	 * Returns the absolute amount.
	 *
	 * @return static
	 */
	public function abs() {
		if ( $this->amount < 0 ) {
			return new static( (int) abs( $this->amount ), $this->scale );
		}

		return $this;
	}

	/**
	 * Adds another price amount.
	 *
	 * @param  Decimal|int|float|string $other Other value.
	 * @return static
	 */
	public function add( $other ) {
		if ( ! $other instanceof self ) {
			$other = static::from_numeric( $other, $this->scale );
		}

		$this->assert_same_scale( $other );

		$result = $this->amount + $other->amount;
		static::validate_integer_bounds( $result );

		return new static( $result, $this->scale );
	}

	/**
	 * Subtracts another price amount.
	 *
	 * @param  Decimal|int|float|string $other Other value.
	 * @return static
	 */
	public function subtract( $other ) {
		if ( ! $other instanceof self ) {
			$other = static::from_numeric( $other, $this->scale );
		}

		$this->assert_same_scale( $other );

		$result = $this->amount - $other->amount;
		static::validate_integer_bounds( $result );

		return new static( $result, $this->scale );
	}

	/**
	 * Subtracts another price amount.
	 *
	 * @param  Decimal|int|float|string $other Other value.
	 * @return static
	 */
	public function sub( $other ) {
		return $this->subtract( $other );
	}

	/**
	 * Multiplies by the given factor.
	 *
	 * This does NOT have to be a price amount, but can be
	 * a simple scalar factor (e.g. 2) as multiplying prices is rarely needed.
	 * However, if a Decimal is passed, its float representation will be used for calculations.
	 *
	 * @param  int|float|Decimal $other         The other value.
	 * @param  int|null          $rounding_mode The rounding mode.
	 * @return static
	 */
	public function multiply( $other, $rounding_mode = null ) {
		$operand = $this->get_scalar_operand( $other );

		$result = $this->amount * $operand;
		static::validate_integer_bounds( $result );

		$result = static::to_int_value( $result, $rounding_mode );

		return new static( $result, $this->scale );
	}

	/**
	 * Multiplies by the given factor.
	 *
	 * @param  int|float|Decimal $other         The other value.
	 * @param  int|null          $rounding_mode The rounding mode.
	 * @return static
	 */
	public function mul( $other, $rounding_mode = null ) {
		return $this->multiply( $other, $rounding_mode );
	}

	/**
	 * Divides by the given divisor.
	 *
	 * This does NOT have to be a price amount, but can be
	 * a simple scalar factor (e.g. 2) as dividing prices is rarely needed.
	 * However, if a Decimal is passed, its float representation will be used for calculations.
	 *
	 * @param  int|float|Decimal $other         The other value.
	 * @param  int|null          $rounding_mode The rounding mode.
	 * @return static
	 *
	 * @throws \LogicException
	 */
	public function divide( $other, $rounding_mode = null ) {
		$operand = $this->get_scalar_operand( $other );
		$epsilon = 10 ** ( - 1 * $this->scale );

		if ( abs( 0 - $operand ) < $epsilon ) {
			throw new \LogicException( 'Division by zero is not allowed' );
		}

		$result = $this->amount / $operand;
		static::validate_integer_bounds( $result );

		$result = static::to_int_value( $result, $rounding_mode );

		return new static( $result, $this->scale );
	}

	/**
	 * Divides by the given divisor.
	 *
	 * @param  int|float|Decimal $other         The other value.
	 * @param  int|null          $rounding_mode The rounding mode.
	 * @return static
	 *
	 * @throws \LogicException
	 */
	public function div( $other, $rounding_mode = null ) {
		return $this->divide( $other, $rounding_mode );
	}

	/**
	 * Returns the additive inverse of a value (e.g. 5 returns -5, -5 returns 5).
	 *
	 * @example Decimal::create(5)->to_additive_inverse() = -5
	 * @example Decimal::create(-5)->to_additive_inverse() = 5
	 *
	 * @return static
	 */
	public function to_additive_inverse() {
		return $this->mul( -1 );
	}

	/**
	 * Calculate a percentage amount.
	 *
	 * @example Decimal::create(100)->to_percentage(30) = 30
	 * @example Decimal::create(50)->to_percentage(50) = 25
	 *
	 * @param  int|float $percentage    The percentage value.
	 * @param  int|null  $rounding_mode The rounding mode.
	 * @return static
	 */
	public function to_percentage( $percentage, $rounding_mode = null ) {
		$percentage = $this->get_scalar_operand( $percentage );

		return $this->mul( $percentage / 100, $rounding_mode );
	}

	/**
	 * Calculate a discounted amount.
	 *
	 * @example Decimal::create(100)->discount(15) = 85
	 *
	 * @param  int|float $discount      The discount (percent) value.
	 * @param  int|null  $rounding_mode The rounding mode.
	 * @return static
	 */
	public function discount( $discount, $rounding_mode = null ) {
		$discount = $this->get_scalar_operand( $discount );

		return $this->sub(
			$this->to_percentage( $discount, $rounding_mode )
		);
	}

	/**
	 * Calculate a surcharge amount.
	 *
	 * @example Decimal::create(100)->surcharge(15) = 115
	 *
	 * @param  int|float $surcharge     The surcharge (percent) value.
	 * @param  int|null  $rounding_mode The rounding mode.
	 * @return static
	 */
	public function surcharge( $surcharge, $rounding_mode = null ) {
		$surcharge = $this->get_scalar_operand( $surcharge );

		return $this->add(
			$this->to_percentage( $surcharge, $rounding_mode )
		);
	}

	/**
	 * Get the relative percentage to another value.
	 *
	 * @example Decimal::create(100)->percentage_of(Decimal::create(50)) = 200
	 * @example Decimal::create(50)->percentage_of(Decimal::create(100)) = 50
	 *
	 * @param  Decimal $other Other Decimal value.
	 * @return int|float
	 */
	public function percentage_of( Decimal $other ) {
		$this->assert_same_scale( $other );

		if ( $this->equals( $other ) ) {
			return 100;
		}

		return ( $this->as_raw_value() * 100 ) / $other->as_raw_value();
	}

	/**
	 * Get the discount percentage starting from a discounted price
	 *
	 * @example Decimal::create(30)->discount_percentage_of(Decimal::create(100)) = 70
	 *
	 * @param  Decimal $other Other Decimal value.
	 * @return int|float
	 */
	public function discount_percentage_of( Decimal $other ) {
		$this->assert_same_scale( $other );

		if ( $this->equals( $other ) ) {
			return 0;
		}

		return 100 - $this->percentage_of( $other );
	}

	/**
	 * Transforms operand into a numeric value used for calculations.
	 *
	 * @param  int|float|Decimal $operand The operand.
	 * @return float
	 *
	 * @throws \InvalidArgumentException
	 */
	protected function get_scalar_operand( $operand ) {
		if ( is_numeric( $operand ) ) {
			return $operand;
		}

		if ( $operand instanceof static ) {
			return $operand->as_numeric();
		}

		throw new \InvalidArgumentException( sprintf(
			'Value "%s" with type "%s" is no valid operand',
			is_scalar( $operand ) ? $operand : (string) $operand,
			is_object( $operand ) ? get_class( $operand ) : gettype( $operand )
		));
	}

	/**
	 * Assert two Decimal has same scale.
	 *
	 * @param  Decimal     $other   Other Decimal.
	 * @param  string|null $message Custom exception message.
	 * @return void
	 *
	 * @throws \DomainException
	 */
	protected function assert_same_scale( Decimal $other, $message = null ) {
		if ( $other->scale !== $this->scale ) {
			$message = $message ?: 'Can\'t operate on amounts with different scales. Please convert both amounts to the same scale before proceeding.';
			throw new \DomainException( $message );
		}
	}

	/**
	 * Default string representation
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->as_string();
	}
}
