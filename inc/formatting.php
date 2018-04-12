<?php

use AweBooking\Support\Decimal;
use AweBooking\Model\Common\Money;

/**
 * Returns the date format.
 *
 * @return string
 */
function abrs_date_format() {
	return apply_filters( 'awebooking/date_format', get_option( 'date_format' ) );
}

/**
 * Returns the time format.
 *
 * @return string
 */
function abrs_time_format() {
	return apply_filters( 'awebooking/time_format', get_option( 'time_format' ) );
}

/**
 * Returns the date time format.
 *
 * @return string
 */
function abrs_datetime_format() {
	/* translators: 1 -Date format, 2 - Time format */
	return apply_filters( 'awebooking/date_time_format', sprintf( esc_html_x( '%1$s %2$s', 'DateTime Format', 'awebooking' ), abrs_date_format(), abrs_time_format() ) );
}

/**
 * Format a date for output.
 *
 * @param  mixed  $date   The date string or DateTimeInterface.
 * @param  string $format Data format.
 * @return string
 */
function abrs_format_datetime( $date, $format = null ) {
	$format = $format ?: abrs_datetime_format();

	$date = abrs_date_time( $date );
	if ( is_null( $date ) ) {
		return '';
	}

	return $date->date_i18n( $format );
}

/**
 * Get the price format depending on the currency position.
 *
 * @return string
 */
function abrs_price_format() {
	$position = abrs_option( 'currency_position' );

	switch ( $position ) {
		case 'left':
			$format = '%1$s%2$s';
			break;
		case 'right':
			$format = '%2$s%1$s';
			break;
		case 'left_space':
			$format = '%1$s&nbsp;%2$s';
			break;
		case 'right_space':
			$format = '%2$s&nbsp;%1$s';
			break;
		default:
			$format = '%1$s%2$s';
			break;
	}

	return apply_filters( 'awebooking/get_price_format', $format, $position );
}

/**
 * Format the price with a currency symbol.
 *
 * @return string
 */
function abrs_price( $price, $currency = null ) {
	// If the given price instanceof Money, just extract
	// the Decimal and currency from it, otherwise convert
	// the price to Decimal object.
	if ( $price instanceof Money ) {
		$currency = $price->get_currency();
		$price = $price->get_amount();
	} elseif ( ! $price instanceof Decimal ) {
		$price = abrs_decimal( $price );
	}

	// Empty the currency, use system currency.
	if ( is_null( $currency ) ) {
		$currency = abrs_current_currency();
	}

	extract( apply_filters( 'awebooking/format_price_args', [ // @codingStandardsIgnoreLine
		'currency'           => $currency,
		'decimal_separator'  => abrs_option( 'price_decimal_separator', '.' ),
		'thousand_separator' => abrs_option( 'price_thousand_separator', ',' ),
		'decimals'           => abrs_option( 'price_number_decimals', 2 ),
		'price_format'       => abrs_price_format(),
	]), EXTR_SKIP );

	// Format the price.
	$formatted_price = apply_filters( 'awebooking/formatted_price',
		number_format( $price->abs()->as_numeric(), $decimals, $decimal_separator, $thousand_separator ),
		$price, $decimals, $decimal_separator, $thousand_separator
	);

	if ( apply_filters( 'awebooking/price_trim_zeros', false ) && $decimals > 0 ) {
		$formatted_price = preg_replace( '/' . preg_quote( $decimal_separator, '/' ) . '0++$/', '', $formatted_price );
	}

	$formatted_price = ( $price->is_negative() ? '-' : '' ) . sprintf( $price_format,
		'<span class="awebooking-price__symbol">' . abrs_currency_symbol( $currency ) . '</span>',
		'<span class="awebooking-price__amount">' . $formatted_price . '</span>'
	);

	$return = '<span class="awebooking-price ' . ( $price->is_negative() ? 'negative' : '' ) . '">' . $formatted_price . '</span>';

	/**
	 * Filters the string of price markup.
	 *
	 * @param string $return 			Price HTML markup.
	 * @param string $price	            Formatted price.
	 * @param array  $args     			Pass on the args.
	 * @param float  $unformatted_price	Price as float to allow plugins custom formatting. Since 3.2.0.
	 */
	return apply_filters( 'awebooking/price', $return, $price );
}
