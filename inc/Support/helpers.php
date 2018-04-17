<?php

use AweBooking\Support\Fluent;
use AweBooking\Support\Decimal;
use AweBooking\Support\Optional;
use AweBooking\Support\Collection;
use AweBooking\Support\Debug\Dumper;

if ( ! function_exists( 'dd' ) ) {
	/**
	 * Dump the passed variables and end the script.
	 *
	 * @param  mixed ...$args The dump arguments.
	 * @return void
	 */
	function dd( ...$args ) {
		foreach ( $args as $x ) {
			( new Dumper )->dump( $x );
		}

		die( 1 );
	}
}

if ( ! function_exists( 'abrs_blank' ) ) {
	/**
	 * Determine if the given value is "blank".
	 *
	 * @param  mixed $value The given value.
	 * @return bool
	 */
	function abrs_blank( $value ) {
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
}

if ( ! function_exists( 'abrs_collect' ) ) {
	/**
	 * Create a collection from the given value.
	 *
	 * @param  mixed $value The given value.
	 * @return \AweBooking\Support\Collection
	 */
	function abrs_collect( $value = null ) {
		return new Collection( $value );
	}
}

if ( ! function_exists( 'abrs_db_transaction' ) ) {
	/**
	 * Run a MySQL transaction query, if supported.
	 *
	 * @param  string $type The transaction type, start (default), commit, rollback.
	 * @return void
	 */
	function abrs_db_transaction( $type = 'start' ) {
		global $wpdb;

		// Hide the errros before perform the action.
		$wpdb->hide_errors();

		switch ( $type ) {
			case 'commit':
				$wpdb->query( 'COMMIT' );
				break;
			case 'rollback':
				$wpdb->query( 'ROLLBACK' );
				break;
			default:
				$wpdb->query( 'START TRANSACTION' );
				break;
		}
	}
}

if ( ! function_exists( 'abrs_decimal' ) ) {
	/**
	 * Create a decimal amount.
	 *
	 * @param  numeric  $amount The amount.
	 * @param  int|null $scale  Optional, custom scale.
	 * @return \AweBooking\Support\Decimal
	 */
	function abrs_decimal( $amount, $scale = null ) {
		return abrs_rescue( function() use ( $amount, $scale ) {
			return Decimal::create( $amount, $scale );
		}, function () { // @codingStandardsIgnoreLine
			return Decimal::zero();
		});
	}
}

if ( ! function_exists( 'abrs_decimal_raw' ) ) {
	/**
	 * Creates a decimal from a raw integer input.
	 *
	 * @param  numeric  $amount The amount.
	 * @param  int|null $scale  Optional, custom scale.
	 * @return \AweBooking\Support\Decimal
	 */
	function abrs_decimal_raw( $amount, $scale = null ) {
		if ( ! filter_var( $amount, FILTER_VALIDATE_INT ) ) {
			return Decimal::zero();
		}

		return abrs_rescue( function() use ( $amount, $scale ) {
			return Decimal::from_raw_value( $amount, $scale );
		}, function () { // @codingStandardsIgnoreLine
			return Decimal::zero();
		});
	}
}

if ( ! function_exists( 'abrs_fluent' ) ) {
	/**
	 * Create a fluent from the given value.
	 *
	 * @param  mixed $value The given value.
	 * @return \AweBooking\Support\Fluent
	 */
	function abrs_fluent( $value = null ) {
		return new Fluent( $value );
	}
}

if ( ! function_exists( 'abrs_html_attributes' ) ) {
	/**
	 * Build an HTML attribute string from an array.
	 *
	 * @param  array $attributes The HTML attributes.
	 * @return string
	 */
	function abrs_html_attributes( $attributes ) {
		$html = [];

		// For numeric keys we will assume that the key and the value are the same
		// as this will convert HTML attributes such as "required" to a correct
		// form like required="required" instead of using incorrect numerics.
		foreach ( (array) $attributes as $key => $value ) {
			if ( is_numeric( $key ) ) {
				$key = $value;
			}

			if ( ! is_null( $value ) ) {
				$html[] = $key . '="' . esc_attr( $value ) . '"';
			}
		}

		return count( $html ) > 0 ? ' ' . implode( ' ', $html ) : '';
	}
}

if ( ! function_exists( 'abrs_html_class' ) ) {
	/**
	 * Returns class string by given an array of classes.
	 *
	 * @param  array $classes The array of class
	 * @return string
	 */
	function abrs_html_class( $classes ) {
		$classes = array_filter( array_unique( (array) $classes ) );

		if ( empty( $classes ) ) {
			return '';
		}

		return implode( ' ', array_map( 'sanitize_html_class', $classes ) );
	}
}

if ( ! function_exists( 'abrs_optional' ) ) {
	/**
	 * Provide access to optional objects.
	 *
	 * @param  mixed $value The given value.
	 * @return \AweBooking\Support\Optional
	 */
	function abrs_optional( $value ) {
		return new Optional( $value );
	}
}

if ( ! function_exists( 'abrs_rescue' ) ) {
	/**
	 * Catch a potential exception and return a default value.
	 *
	 * @param  callable $callback A throwable callback.
	 * @param  mixed    $rescue   Rescue value.
	 * @return mixed
	 */
	function abrs_rescue( callable $callback, $rescue = null ) {
		try {
			return $callback();
		} catch ( Exception $e ) {
			return abrs_value( $rescue );
		} catch ( Throwable $e ) {
			return abrs_value( $rescue );
		}
	}
}

if ( ! function_exists( 'abrs_random_string' ) ) {
	/**
	 * Generate a random string.
	 *
	 * @param  integer $length Random string length.
	 * @return string
	 */
	function abrs_random_string( $length = 16 ) {
		require_once ABSPATH . 'wp-includes/class-phpass.php';

		$bytes = ( new PasswordHash( 8, false ) )->get_random_bytes( $length * 2 );

		return substr( str_replace( [ '/', '+', '=' ], '', base64_encode( $bytes ) ), 0, $length );
	}
}

if ( ! function_exists( 'abrs_str_is' ) ) {
	/**
	 * Determine if a given string matches a given pattern.
	 *
	 * @param  string|array $pattern The pattern.
	 * @param  string       $value   The string.
	 * @return bool
	 */
	function abrs_str_is( $pattern, $value ) {
		$patterns = ! is_array( $pattern ) ? [ $pattern ] : $pattern;

		if ( empty( $patterns ) ) {
			return false;
		}

		foreach ( $patterns as $pattern ) {
			// If the given value is an exact match we can of course return true right
			// from the beginning. Otherwise, we will translate asterisks and do an
			// actual pattern match against the two strings to see if they match.
			if ( $pattern == $value ) {
				return true;
			}

			$pattern = preg_quote( $pattern, '#' );

			// Asterisks are translated into zero-or-more regular expression wildcards
			// to make it convenient to check if the strings starts with the given
			// pattern such as "library/*", making any string check convenient.
			$pattern = str_replace( '\*', '.*', $pattern );

			if ( 1 === preg_match( '#^' . $pattern . '\z#u', $value ) ) {
				return true;
			}
		}

		return false;
	}
}

if ( ! function_exists( 'abrs_value' ) ) {
	/**
	 * Return the default value of the given value.
	 *
	 * @param  mixed $value The given value.
	 * @return mixed
	 */
	function abrs_value( $value ) {
		return $value instanceof Closure ? $value() : $value;
	}
}

if ( ! function_exists( 'abrs_valid_url' ) ) {
	/**
	 * Determine if the given "path" is a valid URL.
	 *
	 * @param  string $path The input URL to check.
	 * @return bool
	 */
	function abrs_valid_url( $path ) {
		if ( preg_match( '~^(#|//|https?://|mailto:|tel:)~', $path ) ) {
			return true;
		}

		return filter_var( $path, FILTER_VALIDATE_URL ) !== false;
	}
}

if ( ! function_exists( 'abrs_is_request' ) ) {
	/**
	 * What type of request is this?
	 *
	 * @param  string $type admin, ajax, cron or frontend.
	 * @return bool
	 */
	function abrs_is_request( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_admin();
			case 'ajax':
				return defined( 'DOING_AJAX' );
			case 'cron':
				return defined( 'DOING_CRON' );
			case 'frontend':
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}
}
