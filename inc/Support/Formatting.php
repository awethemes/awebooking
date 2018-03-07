<?php
namespace AweBooking\Support;

use DateTime;
use AweBooking\AweBooking;
use AweBooking\Money\Money;
use AweBooking\Money\Currency;

class Formatting {
	/**
	 * Format a number with grouped thousands.
	 *
	 * @param  float   $number     The number being formatted.
	 * @param  boolean $trim_zeros Trim zeros or not.
	 * @return string
	 */
	public static function number( $number, $trim_zeros = false ) {
		$number = ( $number instanceof Decimal ) ? $number->as_numeric() : $number;

		$config = awebooking()->make( 'setting' );

		$args = apply_filters( 'awebooking/number_format_args', [
			'number_decimals'    => $config->get( 'price_number_decimals' ),
			'decimal_separator'  => $config->get( 'price_decimal_separator' ),
			'thousand_separator' => $config->get( 'price_thousand_separator' ),
		]);

		$formatted = number_format( $number, absint( $args['number_decimals'] ), $args['decimal_separator'], $args['thousand_separator'] );

		if ( $trim_zeros ) {
			$formatted = preg_replace( '/' . preg_quote( $config->get( 'price_decimal_separator' ), '/' ) . '0++$/', '', $formatted );
		}

		return apply_filters( 'awebooking/number_formatted', $formatted, $number, $args );
	}

	/**
	 * Format the money to human readable.
	 *
	 * @param  mixed   $amount     The amount.
	 * @param  boolean $trim_zeros Trim zeros or not.
	 * @return string
	 */
	public static function money( $amount, $trim_zeros = false ) {
		if ( ! $amount instanceof Money ) {
			$amount = Money::of( $amount );
		}

		// Do we have a negative amount?
		$negative = $amount->is_negative() ? '-' : '';

		$negative_amount  = $amount->abs()->get_amount();
		$formatted_amount = static::number( $negative_amount->as_string(), $trim_zeros );

		$formatted = sprintf( awebooking( 'setting' )->get_money_format(),
			'<span class="awebooking-price__amount">' . $formatted_amount . '</span>',
			'<span class="awebooking-price__symbol">' . $amount->get_currency()->get_symbol() . '</span>'
		);

		$formatted = '<span class="awebooking-price">' . ( $negative . $formatted ) . '</span>';

		return apply_filters( 'awebooking/price_format', $formatted, $amount );
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
