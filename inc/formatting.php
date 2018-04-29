<?php

/**
 * Clean variables using sanitize_text_field.
 *
 * Arrays are cleaned recursively.
 *
 * @param  string|array $var Data to sanitize.
 * @return string|array
 */
function abrs_clean( $var ) {
	return abrs_recursive_sanitizer( $var, 'sanitize_text_field' );
}

/**
 * Escaping for text with few html allowed.
 *
 * @param  string $text Input text.
 * @return string
 */
function abrs_esc_text( $text ) {
	static $allowed_html = [  // @codingStandardsIgnoreStart
		'a'       => [ 'href' => true, 'target' => true, 'title' => true ],
		'abbr'    => [ 'title' => true ],
		'acronym' => [ 'title' => true ],
		'code'    => true,
		'em'      => true,
		'strong'  => true,
	]; // @codingStandardsIgnoreEnd

	return wp_kses( $text, $allowed_html );
}

/**
 * Converts given content to plain text.
 *
 * @param  string $content The content to convert.
 * @return string
 */
function abrs_esc_plan_text( $content ) {
	if ( empty( $content ) ) {
		return '';
	}

	static $replacements = [
		"/\r/"                                                  => '',     // Non-legal carriage return.
		'/&(nbsp|#0*160);/i'                                    => ' ',    // Non-breaking space.
		'/&(quot|rdquo|ldquo|#0*8220|#0*8221|#0*147|#0*148);/i' => '"',    // Double quotes.
		'/&(apos|rsquo|lsquo|#0*8216|#0*8217);/i'               => "'",    // Single quotes.
		'/&gt;/i'                                               => '>',    // Greater-than.
		'/&lt;/i'                                               => '<',    // Less-than.
		'/&#0*38;/i'                                            => '&',    // Ampersand.
		'/&amp;/i'                                              => '&',    // Ampersand.
		'/&(copy|#0*169);/i'                                    => '(c)',  // Copyright.
		'/&(trade|#0*8482|#0*153);/i'                           => '(tm)', // Trademark.
		'/&(reg|#0*174);/i'                                     => '(R)',  // Registered.
		'/&(mdash|#0*151|#0*8212);/i'                           => '--',   // mdash.
		'/&(ndash|minus|#0*8211|#0*8722);/i'                    => '-',    // ndash.
		'/&(bull|#0*149|#0*8226);/i'                            => '*',    // Bullet.
		'/&(pound|#0*163);/i'                                   => '£',    // Pound sign.
		'/&(euro|#0*8364);/i'                                   => 'EUR',  // Euro sign.
		'/&(dollar|#0*36);/i'                                   => '$',    // Dollar sign.
		'/&[^&\s;]+;/i'                                         => '',     // Unknown/unhandled entities.
		'/[ ]{2,}/'                                             => ' ',    // Runs of spaces, post-handling.
	];

	return preg_replace( array_keys( $replacements ), array_values( $replacements ), strip_tags( $content ) );
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
