<?php

use AweBooking\Support\Decimal;
use AweBooking\Support\Fluent;
use AweBooking\Support\Optional;
use AweBooking\Support\Collection;

if ( ! function_exists( 'dd' ) ) {
	/**
	 * Dump the passed variables and end the script.
	 *
	 * @param  mixed ...$args The dump arguments.
	 * @return void
	 */
	function dd( ...$args ) {
		foreach ( $args as $x ) {
			function_exists( 'dump' ) ? dump( $x ) : var_dump( $x );
		}

		die( 1 );
	}
}

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
function abrs_collect( $value = null ) {
	return new Collection( $value );
}

/**
 * Create a decimal amount.
 *
 * @param  int|float|Decimal $amount The amount.
 * @param  int|null          $scale  Optional, custom scale.
 * @return \AweBooking\Support\Decimal
 */
function abrs_decimal( $amount = 0, $scale = null ) {
	return abrs_rescue( function () use ( $amount, $scale ) {
		return Decimal::create( $amount, $scale );
	}, function () { // @codingStandardsIgnoreLine
		return Decimal::zero();
	} );
}

/**
 * Creates a decimal from a raw integer input.
 *
 * @param  int|float $amount The amount.
 * @param  int|null  $scale  Optional, custom scale.
 * @return \AweBooking\Support\Decimal
 */
function abrs_decimal_raw( $amount, $scale = null ) {
	if ( ! filter_var( $amount, FILTER_VALIDATE_INT ) ) {
		return Decimal::zero();
	}

	return abrs_rescue( function () use ( $amount, $scale ) {
		return Decimal::from_raw_value( $amount, $scale );
	}, function () { // @codingStandardsIgnoreLine
		return Decimal::zero();
	} );
}

/**
 * Create a fluent from the given value.
 *
 * @param  mixed $value The given value.
 * @return \AweBooking\Support\Fluent
 */
function abrs_fluent( $value = null ) {
	return new Fluent( $value );
}

/**
 * Build an HTML attribute string from an array.
 *
 * @param  array $attributes The HTML attributes.
 * @return string
 */
function abrs_html_attributes( $attributes ) {
	$html = [];

	foreach ( (array) $attributes as $key => $value ) {
		$element = _abrs_build_attribute_element( $key, $value );

		if ( ! is_null( $element ) ) {
			$html[] = $element;
		}
	}

	return count( $html ) > 0 ? ' ' . implode( ' ', $html ) : '';
}

/**
 * Build a single attribute element.
 *
 * @param string $key
 * @param string $value
 * @return string|null
 */
function _abrs_build_attribute_element( $key, $value ) {
	// For numeric keys we will assume that the value is a boolean attribute
	// where the presence of the attribute represents a true value and the
	// absence represents a false value.
	if ( is_numeric( $key ) ) {
		return $value;
	}

	// Treat boolean attributes as HTML properties.
	if ( is_bool( $value ) && 'value' !== $key ) {
		return $value ? $key : '';
	}

	if ( is_array( $value ) && 'class' === $key ) {
		return 'class="' . abrs_html_class( $value ) . '"';
	}

	if ( ! is_null( $value ) ) {
		return $key . '="' . abrs_clean( $value ) . '"';
	}

	return null;
}

/**
 * Returns class string by given an array of classes.
 *
 * @param  array $classes The array of class.
 * @return string
 */
function abrs_html_class( $classes ) {
	$classes = array_filter( array_unique( (array) $classes ) );

	if ( empty( $classes ) ) {
		return '';
	}

	return implode( ' ', array_map( 'sanitize_html_class', $classes ) );
}

/**
 * Provide access to optional objects.
 *
 * @param  mixed $value The given value.
 * @return \AweBooking\Support\Optional
 */
function abrs_optional( $value ) {
	return new Optional( $value );
}

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

	foreach ( $patterns as $_pattern ) {
		// If the given value is an exact match we can of course return true right
		// from the beginning. Otherwise, we will translate asterisks and do an
		// actual pattern match against the two strings to see if they match.
		if ( $_pattern == $value ) {
			return true;
		}

		$_pattern = preg_quote( $_pattern, '#' );

		// Asterisks are translated into zero-or-more regular expression wildcards
		// to make it convenient to check if the strings starts with the given
		// pattern such as "library/*", making any string check convenient.
		$_pattern = str_replace( '\*', '.*', $_pattern );

		if ( 1 === preg_match( '#^' . $_pattern . '\z#u', $value ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Return the default value of the given value.
 *
 * @param  mixed $value The given value.
 * @return mixed
 */
function abrs_value( $value ) {
	return $value instanceof Closure ? $value() : $value;
}

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

/**
 * Sets time_limit if it is enabled.
 *
 * @param  int $limit The time limit.
 * @return void
 */
function abrs_set_time_limit( $limit = 0 ) {
	if ( function_exists( 'set_time_limit' ) &&
	     false === strpos( ini_get( 'disable_functions' ), 'set_time_limit' ) ) {
		@set_time_limit( $limit ); // @codingStandardsIgnoreLine
	}
}

/**
 * Sort the array by given a arbitrary ordering.
 *
 * @param array $array    The array to sort.
 * @param array $ordering The ordering.
 *
 * @return array
 */
function abrs_sort_by_keys( array $array, $ordering = [] ) {
	// Since we don't have any ordering, return the original array.
	if ( empty( $ordering ) ) {
		return $array;
	}

	$is_assoc = array_values( $array ) !== $array;

	$bottom = count( $array ) + 10001;
	$sorted = [];

	foreach ( $array as $key => $value ) {
		if ( ! $is_assoc ) {
			$key = $value;
		}

		// Found the possiton in $ordering.
		$index = array_search( $key, $ordering );

		// Found in $ordering, just add by that position,
		// otherwise we will add to end of the $sorted.
		if ( false !== $index ) {
			$sorted[ $index ] = $value;
		} else {
			$sorted[ $bottom ] = $value;
			$bottom++;
		}
	}

	// Sort by index.
	ksort( $sorted );

	return array_values( $sorted );
}
