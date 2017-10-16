<?php
namespace AweBooking\Support;

use Closure;
use Exception;
use Psr\Log\LoggerInterface;

class Utils {
	/**
	 * Determine if the given value is "blank".
	 *
	 * @param  mixed $value The given value.
	 * @return bool
	 */
	public static function blank( $value ) {
		if ( is_null( $value ) ) {
			return true;
		}

		if ( is_string( $value ) ) {
			return trim( $value ) === '';
		}

		if ( is_numeric( $value ) || is_bool( $value ) ) {
			return false;
		}

		if ( $value instanceof Countable ) {
			return count( $value ) === 0;
		}

		return empty( $value );
	}

	/**
	 * Create a collection from the given value.
	 *
	 * @param  mixed $value The given value.
	 * @return \AweBooking\Support\Collection
	 */
	public static function collect( $value = null ) {
		return new Collection( $value );
	}

	/**
	 * Provide access to optional objects.
	 *
	 * @param  mixed $value The given value.
	 * @return \AweBooking\Support\Optional
	 */
	public static function optional( $value ) {
		return new Optional( $value );
	}

	/**
	 * Report an exception.
	 *
	 * @param  Exception $e Report the exception.
	 * @return void
	 */
	public static function report( $e ) {
		try {
			$logger = awebooking()->make( LoggerInterface::class );
		} catch ( Exception $ex ) {
			throw $e; // throw the original exception.
		}

		$logger->error( $e->getMessage(), [ 'exception' => $e ] );
	}

	/**
	 * Catch a potential exception and return a default value.
	 *
	 * @param  callable $callback A throwable callback.
	 * @param  mixed    $rescue   Rescue value.
	 * @return mixed
	 */
	public static function rescue( callable $callback, $rescue = null ) {
		try {
			return $callback();
		} catch ( Exception $e ) {
			static::report( $e );

			return static::value( $rescue );
		}
	}

	/**
	 * Return the default value of the given value.
	 *
	 * @param  mixed $value The given value.
	 * @return mixed
	 */
	public static function value( $value ) {
		return $value instanceof Closure ? $value() : $value;
	}
}
