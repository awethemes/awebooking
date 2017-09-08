<?php
namespace AweBooking\Support;

use DateTime;
use AweBooking\AweBooking;
use AweBooking\Pricing\Price;
use AweBooking\Currency\Currency;

class Formatting {
	/**
	 * Format a number with grouped thousands.
	 *
	 * @param float $number The number being formatted.
	 * @param array $args   The arguments for formatting.
	 * @return string
	 */
	public static function number_format( $number, array $args = [] ) {
		$config = awebooking()->make( 'config' );

		$args = apply_filters( 'awebooking/number_format_args', wp_parse_args( $args, [
			'trim_zeros'         => true,
			'number_decimals'    => $config->get( 'price_number_decimals' ),
			'decimal_separator'  => $config->get( 'price_decimal_separator' ),
			'thousand_separator' => $config->get( 'price_thousand_separator' ),
		]));

		$formatted = number_format( $number, absint( $args['number_decimals'] ), $args['decimal_separator'], $args['thousand_separator'] );

		if ( $args['trim_zeros'] ) {
			$formatted = preg_replace( '/' . preg_quote( $config->get( 'price_decimal_separator' ), '/' ) . '0++$/', '', $formatted );
		}

		return apply_filters( 'awebooking/number_format', $formatted, $number, $args );
	}

	/**
	 * Get the price format depending on the currency position.
	 *
	 * @return string
	 */
	public static function get_price_format() {
		$position = awebooking( 'config' )->get( 'currency_position' );
		$format = '%1$s%2$s';

		switch ( $position ) {
			case Currency::POS_LEFT:
				$format = '%2$s%1$s';
			break;

			case Currency::POS_RIGHT:
				$format = '%1$s%2$s';
			break;

			case Currency::POS_LEFT_SPACE:
				$format = '%2$s&nbsp;%1$s';
			break;

			case Currency::POS_RIGHT_SPACE:
				$format = '%1$s&nbsp;%2$s';
			break;
		}

		return apply_filters( 'awebooking/get_price_format', $format, $position );
	}

	/**
	 * Format a price.
	 *
	 * @param  numeric $price The price being formatted.
	 * @param  array   $args  Number format arguments, @see static::number_format() method.
	 * @return string
	 */
	public static function price_format( $price, array $args = [] ) {
		// If we pass a Price instance, using
		// that amount and get currency from Price.
		if ( $price instanceof Price ) {
			$currency = $price->get_currency();
			$price    = $price->get_amount();
		} else {
			// Otherwise, use default currency.
			$currency = awebooking( 'currency' );
		}

		// Do we have a negative price?
		$negative = $price < 0 ? '-' : '';
		$price    = $negative ? $price * -1 : $price;

		$price = static::number_format( $price, $args );
		$formatted = sprintf( static::get_price_format(), $price, $currency->get_symbol() );

		return apply_filters( 'awebooking/price_format', ( $negative . $formatted ), $price, $currency );
	}

	/**
	 * Sanitize, remove locale formatting, and optionally round.
	 *
	 * @param  float|string $number Expects either a float or a string with a decimal separator only (no thousands).
	 * @param  bool|int     $dp     Using number of decimal points or not.
	 * @return float
	 */
	public static function format_decimal( $number, $dp = false ) {
		$locale = localeconv();
		$config = awebooking()->make( 'config' );

		// If not float number, clean input number and remove locale decimals.
		// Then keep only numeric, '-', comma and dot character.
		if ( ! is_float( $number ) ) {
			$decimals = [ $config->get( 'price_decimal_separator' ), $locale['decimal_point'] ];
			$number   = str_replace( $decimals, '.', sanitize_text_field( $number ) );
			$number   = floatval( preg_replace( '/[^0-9\.,-]/', '', $number ) );
		}

		if ( false !== $dp ) {
			$dp     = absint( true === $dp ? $config->get( 'price_number_decimals' ) : $dp );
			$number = floatval( number_format( $number, $dp, '.', '' ) );
		}

		return $number;
	}

	/**
	 * Convert decimal to amount (integer).
	 *
	 * The BAT system required using integer for core process.
	 * So, we need convert decimal to integer depend `price_number_decimals` of currency.
	 *
	 * Eg: 10.99 -> 1099, 10.999 -> 1099
	 *
	 * @see https://github.com/Roomify/bat/issues/20
	 *
	 * @param  float $decimal Decimal number.
	 * @return int
	 */
	public static function decimal_to_amount( $decimal ) {
		$decimal  = static::format_decimal( $decimal, true );

		$decimals = absint( awebooking( 'config' )->get( 'price_number_decimals' ) );
		$factor   = pow( 10, $decimals );

		return $decimal * $factor;
	}

	/**
	 * Convert amount to decimal.
	 *
	 * @param  integer $amount The amount.
	 * @return float
	 */
	public static function amount_to_decimal( $amount ) {
		$decimals = absint( awebooking( 'config' )->get( 'price_number_decimals' ) );
		$divisor  = pow( 10, $decimals );

		return floatval( (int) $amount / $divisor );
	}

	/**
	 * Format the date.
	 *
	 * @param  DateTime $datetime DateTime instance.
	 * @param  string   $format   Display date time format.
	 * @return string
	 */
	public static function date_format( DateTime $datetime, $format = null ) {
		if ( ! $format ) {
			$format = awebooking( 'setting' )->get_date_format();
		}

		return date_i18n( $format, $datetime->getTimestamp() );
	}

	/**
	 * Takes a php date() format string and returns a string formatted to suit for the date/time pickers
	 * It will work with only with the following subset ot date() options: d, j, z, m, n, y, and Y.
	 *
	 * A slight effort is made to deal with escaped characters.
	 *
	 * Other options are ignored, because they would either bring compatibility problems between PHP and JS, or
	 * bring even more translation troubles.
	 *
	 * @param  string $format php date format //.
	 * @return string
	 */
	public static function php_to_js_dateformat( $format ) {
		// Order is relevant here, since the replacement will be done sequentially.
		$supported_options = array(
			'd' => 'dd',  // Day, leading 0
			'j' => 'd',   // Day, no 0
			'z' => 'o',   // Day of the year, no leading zeroes,
			// 'D' => 'D',   // Day name short, not sure how it'll work with translations
			// 'l' => 'DD',  // Day name full, idem before
			'm' => 'mm',  // Month of the year, leading 0
			'n' => 'm',   // Month of the year, no leading 0
			// 'M' => 'M',   // Month, Short name
			'F' => 'MM',  // Month, full name,
			'y' => 'y',   // Year, two digit
			'Y' => 'yy',  // Year, full
			'H' => 'HH',  // Hour with leading 0 (24 hour)
			'G' => 'H',   // Hour with no leading 0 (24 hour)
			'h' => 'hh',  // Hour with leading 0 (12 hour)
			'g' => 'h',   // Hour with no leading 0 (12 hour),
			'i' => 'mm',  // Minute with leading 0,
			's' => 'ss',  // Second with leading 0,
			'a' => 'tt',  // am/pm
			'A' => 'TT',  // AM/PM.
		);

		foreach ( $supported_options as $php => $js ) {
			// Replaces every instance of a supported option, but skips escaped characters.
			$format = preg_replace( "~(?<!\\\\)$php~", $js, $format );
		}

		$format = preg_replace_callback( '~(?:\\\.)+~', array( 'CMB2_Utils', 'wrap_escaped_chars' ), $format );

		return $format;
	}
}
