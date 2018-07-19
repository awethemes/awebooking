<?php
namespace AweBooking\Deprecated\Support;

use DateTime;

class Formatting {
	public static function number_format( $number, array $args = [] ) {
		_abrs_310_deprecated_function( __FUNCTION__ );
	}

	public static function get_price_format() {
		_abrs_310_deprecated_function( __FUNCTION__ );
		return abrs_get_price_format();
	}

	public static function price_format( $price, array $args = [] ) {
		_abrs_310_deprecated_function( __FUNCTION__ );
		return abrs_format_price( $price );
	}

	public static function format_decimal( $number, $dp = false ) {
		_abrs_310_deprecated_function( __FUNCTION__ );
		return abrs_sanitize_decimal( $number );
	}

	public static function decimal_to_amount( $decimal ) {
		_abrs_310_deprecated_function( __FUNCTION__ );

		$decimal  = static::format_decimal( $decimal );

		$decimals = absint( abrs_get_option( 'price_number_decimals' ) );
		$factor   = pow( 10, $decimals );

		return $decimal * $factor;
	}

	public static function amount_to_decimal( $amount ) {
		_abrs_310_deprecated_function( __FUNCTION__ );

		$decimals = absint( abrs_get_option( 'price_number_decimals' ) );

		$divisor  = pow( 10, $decimals );

		return floatval( (int) $amount / $divisor );
	}

	public static function date_format( DateTime $datetime, $format = null ) {
		_abrs_310_deprecated_function( __FUNCTION__ );
		return abrs_format_date( $datetime, $format );
	}

	public static function php_to_js_dateformat( $format ) {
		_abrs_310_deprecated_function( __FUNCTION__ );
		return \CMB2_Utils::php_to_js_dateformat( $format );
	}
}
