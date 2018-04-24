<?php

/**
 * Format a date time for output.
 *
 * @param  mixed  $date_time The date string or DateTimeInterface.
 * @param  string $format    Data format.
 * @return string
 */
function abrs_format_datetime( $date_time, $format = null ) {
	$format = $format ?: abrs_datetime_format();

	$date_time = abrs_date_time( $date_time );

	return ! is_null( $date_time )
		? $date_time->date_i18n( $format )
		: '';
}

/**
 * Format a date for output.
 *
 * @param  mixed  $date   The date string or DateTimeInterface.
 * @param  string $format Data format.
 * @return string
 */
function abrs_format_date( $date, $format = null ) {
	return abrs_format_datetime( $date, $format ?: abrs_date_format() );
}

/**
 * Get the price format depending on the currency position.
 *
 * @return string
 */
function abrs_get_price_format() {
	$position = abrs_get_option( 'currency_position' );

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
 * @param  int|float $amount   The price amount.
 * @param  string    $currency The currency, default is current currency.
 * @return string
 */
function abrs_format_price( $amount, $currency = null ) {
	// Convert amount to Decimal.
	$amount = abrs_decimal( $amount );

	// Prepare the price format args.
	$args = apply_filters( 'awebooking/format_price_args', [
		'currency'           => $currency ?: abrs_current_currency(), // Fallback use default currency.
		'price_format'       => abrs_get_price_format(),
		'decimals'           => abrs_get_option( 'price_number_decimals', 2 ),
		'decimal_separator'  => abrs_get_option( 'price_decimal_separator', '.' ),
		'thousand_separator' => abrs_get_option( 'price_thousand_separator', ',' ),
	]);

	// Format amount use number_format().
	$formatted = number_format( $amount->abs()->as_numeric(), $args['decimals'], $args['decimal_separator'], $args['thousand_separator'] );
	$formatted = apply_filters( 'awebooking/price_formatted', $formatted, $amount, $args );

	if ( apply_filters( 'awebooking/price_trim_zeros', false ) && $args['decimals'] > 0 ) {
		$formatted = preg_replace( '/' . preg_quote( $args['decimal_separator'], '/' ) . '0++$/', '', $formatted );
	}

	$formatted = ( $amount->is_negative() ? '-' : '' ) . sprintf( $args['price_format'],
		'<span class="awebooking-price__symbol">' . abrs_currency_symbol( $args['currency'] ) . '</span>',
		'<span class="awebooking-price__amount">' . $formatted . '</span>'
	);

	// Build the return markup.
	$return = '<span class="awebooking-price ' . ( $amount->is_negative() ? 'negative' : '' ) . '">' . $formatted . '</span>';

	/**
	 * Filters the string of price markup.
	 *
	 * @param string                      $return Price HTML markup.
	 * @param \AweBooking\Support\Decimal $amount The amount as Decimal.
	 * @param array                       $args   Price format args.
	 */
	return apply_filters( 'awebooking/price_formatted_markup', $return, $amount, $args );
}

/**
 * Same as abrs_format_price() but echo the price instead.
 *
 * @param  int|float $amount   The amount.
 * @param  string    $currency The currency, default is current currency.
 * @return void
 */
function abrs_price( $amount, $currency = null ) {
	echo abrs_format_price( $amount, $currency ); // WPCS: XSS OK.
}

/**
 * Escaping for text with few html allowed.
 *
 * @param  string $text Input text.
 * @return string
 */
function abrs_esc_text( $text ) {
	static $allowed_html = [
		'a'       => [ 'href' => true, 'title' => true ], // @codingStandardsIgnoreLine
		'abbr'    => [ 'title' => true ],
		'acronym' => [ 'title' => true ],
		'code'    => true,
		'em'      => true,
		'strong'  => true,
	];

	return wp_kses( $text, $allowed_html );
}
