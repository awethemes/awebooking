<?php

use AweBooking\Model\Common\Timespan;
use AweBooking\Model\Common\Guest_Counts;
use AweBooking\Component\Country\Formatter as Country_Formatter;

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
 * Convert plaintext phone number to clickable phone number.
 *
 * Remove formatting and allow "+".
 * Example and specs: https://developer.mozilla.org/en/docs/Web/HTML/Element/a#Creating_a_phone_link
 *
 * @param  string $phone Content to convert phone number.
 * @return string
 */
function abrs_make_phone_clickable( $phone ) {
	$number = trim( preg_replace( '/[^\d|\+]/', '', $phone ) );

	return '<a href="tel:' . esc_attr( $number ) . '">' . esc_html( $phone ) . '</a>';
}

/**
 * Returns the date format.
 *
 * @return string
 */
function abrs_get_date_format() {
	return apply_filters( 'abrs_date_format', get_option( 'date_format' ) );
}

/**
 * Returns the time format.
 *
 * @return string
 */
function abrs_get_time_format() {
	return apply_filters( 'abrs_time_format', get_option( 'time_format' ) );
}

/**
 * Returns the date time format.
 *
 * @return string
 */
function abrs_get_datetime_format() {
	return apply_filters( 'abrs_date_time_format',
		/* translators: 1 -Date format, 2 - Time format */
		sprintf( esc_html_x( '%1$s %2$s', 'DateTime Format', 'awebooking' ), abrs_get_date_format(), abrs_get_time_format() )
	);
}

/**
 * Format a date for output.
 *
 * @param  mixed  $date   The date string or DateTimeInterface.
 * @param  string $format Data format.
 * @return string
 */
function abrs_format_date( $date, $format = null ) {
	return abrs_format_date_time( $date, $format ?: abrs_get_date_format() );
}

/**
 * Format a date time for output.
 *
 * @param  mixed  $date_time The date string or DateTimeInterface.
 * @param  string $format    Data format.
 * @return string
 */
function abrs_format_date_time( $date_time, $format = null ) {
	$format = $format ?: abrs_get_datetime_format();

	$date_time = abrs_date_time( $date_time );

	return ! is_null( $date_time )
		? $date_time->date_i18n( $format )
		: '';
}

/**
 * Format the given address.
 *
 * @see AweBooking\Component\Country\Formatter::format()
 *
 * @param  array $args An array of address.
 * @return string
 */
function abrs_format_address( $args ) {
	return ( new Country_Formatter )->format( $args );
}

/**
 * Get the price format depending on the currency position.
 *
 * %1$s The currency symbol
 * %2$s The amount.
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

	return apply_filters( 'abrs_get_price_format', $format, $position );
}

/**
 * Format the price with a currency symbol.
 *
 * @param  \AweBooking\Support\Decimal|int|float $amount   The amount.
 * @param  string                                $currency The currency, default is current currency.
 * @return string
 */
function abrs_format_price( $amount, $currency = null ) {
	// Convert amount to Decimal.
	$amount = abrs_decimal( $amount );

	// Prepare the price format args.
	$args = apply_filters( 'abrs_format_price_args', [
		'currency'           => $currency ?: abrs_current_currency(), // Fallback use default currency.
		'price_format'       => abrs_get_price_format(),
		'decimals'           => abrs_get_option( 'price_number_decimals', 2 ),
		'decimal_separator'  => abrs_get_option( 'price_decimal_separator', '.' ),
		'thousand_separator' => abrs_get_option( 'price_thousand_separator', ',' ),
	]);

	// Format amount use number_format().
	$formatted = number_format( $amount->abs()->as_numeric(), $args['decimals'], $args['decimal_separator'], $args['thousand_separator'] );
	$formatted = apply_filters( 'abrs_price_formatted', $formatted, $amount, $args );

	if ( $args['decimals'] > 0 && apply_filters( 'abrs_price_trim_zeros', false ) ) {
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
	return apply_filters( 'abrs_price_formatted_markup', $return, $amount, $args );
}

/**
 * Same as abrs_format_price() but echo the price instead.
 *
 * @param  \AweBooking\Support\Decimal|int|float $amount   The amount.
 * @param  string                                $currency The currency, default is current currency.
 * @return void
 */
function abrs_price( $amount, $currency = null ) {
	echo abrs_format_price( $amount, $currency ); // WPCS: XSS OK.
}

/**
 * Retrieves translated of "room count" in singular or plural.
 *
 * @param int $count The number of rooms.
 * @return string
 */
function abrs_ngettext_nights( $count ) {
	if ( $count instanceof Timespan ) {
		$count = $count->get_nights();
	}

	/* translators: Number of nights */
	$string = sprintf( _n( '%s night', '%s nights', $count, 'awebooking' ), number_format_i18n( $count ) );

	return apply_filters( 'abrs_ngettext_nights', $string, $count );
}

/**
 * Retrieves translated of "room count" in singular or plural.
 *
 * @param int $count The number of rooms.
 * @return string
 */
function abrs_ngettext_rooms( $count ) {
	/* translators: Number of rooms */
	$string = sprintf( _n( '%s room', '%s rooms', $count, 'awebooking' ), number_format_i18n( $count ) );

	return apply_filters( 'abrs_ngettext_rooms', $string, $count );
}

/**
 * Return guest counts formating.
 *
 * @param  Guest_Counts|int $adults   The number of adults.
 * @param  int              $children The number of children.
 * @param  int              $infants  The number of infants.
 * @return string
 */
function abrs_format_guest_counts( $adults, $children = 0, $infants = 0 ) {
	$guest_counts = $adults;

	if ( ! $adults instanceof Guest_Counts ) {
		$guest_counts = new Guest_Counts( $adults, $children, $infants );
	}

	$html = sprintf(
		'<span class="awebooking_guest__adults">%1$d %2$s</span>',
		esc_html( $guest_counts->get_adults()->get_count() ),
		esc_html( _n( 'adult', 'adults', $guest_counts->get_adults()->get_count(), 'awebooking' ) )
	);

	if ( $children = $guest_counts->get_children() ) {
		$html .= sprintf(
			' , <span class="awebooking_guest__children">%1$d %2$s</span>',
			esc_html( $children->get_count() ),
			esc_html( _n( 'child', 'children', $children->get_count(), 'awebooking' ) )
		);
	}

	if ( $infants = $guest_counts->get_infants() ) {
		$html .= sprintf(
			' &amp; <span class="awebooking_guest__infants">%1$d %2$s</span>',
			esc_html( $infants->get_count() ),
			esc_html( _n( 'infant', 'infants', $infants->get_count(), 'awebooking' ) )
		);
	}

	return apply_filters( 'abrs_format_guest_counts', $html, $guest_counts );
}

/**
 * Return measure unit label.
 *
 * @return string
 */
function abrs_get_measure_unit_label() {
	$measure_unit = abrs_get_option( 'measure_unit', 'm2' );

	switch ( $measure_unit ) {
		case 'm2':
			$format = 'm<sup>2</sup>';
			break;
		case 'ft2':
			$format = 'ft<sup>2</sup>';
			break;
		default:
			$format = 'm<sup>2</sup>';
			break;
	}

	return apply_filters( 'abrs_get_measure_unit_label', $format, $measure_unit );
}

/**
 * Return service describe formating.
 *
 * @param  int|float $amount    The amount.
 * @param  string    $operation The operation.
 *
 * @return string
 */
function abrs_format_service_price( $amount, $operation = 'add' ) {
	$label = '';

	switch ( $operation ) {
		case 'add':
			/* translators: %s amount */
			$label = sprintf( esc_html__( '+ %s to price', 'awebooking' ), abrs_format_price( $amount ) );
			break;

		case 'add_daily':
			/* translators: %s amount */
			$label = sprintf( esc_html__( '+ %s x night to price', 'awebooking' ), abrs_format_price( $amount ) );
			break;

		case 'increase':
			/* translators: %1$s amount */
			$label = sprintf( __( '+ <span>%1$s%%</span> to price', 'awebooking' ), $amount );
			break;
	}

	return apply_filters( 'abrs_format_service_price', $label, $amount, $operation );
}
