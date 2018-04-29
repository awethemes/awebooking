<?php

/**
 * Recursive sanitize a given values.
 *
 * @param  mixed  $values    The values.
 * @param  string $sanitizer The sanitizer callback.
 * @return mixed
 */
function abrs_recursive_sanitizer( $values, $sanitizer ) {
	if ( ! is_array( $values ) ) {
		return $sanitizer( $values );
	}

	foreach ( $values as $key => &$value ) {
		if ( is_array( $value ) ) {
			$value = abrs_recursive_sanitizer( $value, $sanitizer );
		} else {
			$value = $sanitizer( $value );
		}
	}

	return $values;
}

/**
 * Sanitizes a simple text string.
 *
 * @param  mixed $value The string to sanitize.
 * @return string
 */
function abrs_sanitize_text( $value ) {
	return sanitize_text_field( wp_unslash( $value ) );
}

/**
 * Sanitizes content that could contain HTML.
 *
 * @param  mixed $value The HTML string to sanitize.
 * @return string
 */
function abrs_sanitize_html( $value ) {
	return balanceTags( wp_kses_post( $value ), true );
}

/**
 * Sanitizes for checkbox or toggle.
 *
 * @param  mixed $value The checkbox value.
 * @return string
 */
function abrs_sanitize_checkbox( $value ) {
	return in_array( $value, [ 'on', 'yes', '1', 1, true ], true ) ? 'on' : 'off';
}

/**
 * Sanitize decimal number.
 *
 * @param  mixed $number The raw number value.
 * @return string
 */
function abrs_sanitize_decimal( $number ) {
	$locale = localeconv();
	$decimals = [ abrs_get_option( 'price_decimal_separator', '.' ), $locale['decimal_point'], $locale['mon_decimal_point'] ];

	// If not float number, clean input number and remove locale decimals.
	// Then keep only numeric, '-', comma and dot character.
	if ( ! is_float( $number ) ) {
		$number = str_replace( $decimals, '.', sanitize_text_field( $number ) );
		$number = preg_replace( '/[^0-9\.,-]/', '', $number );
	}

	// Conver the number to Decimal object then convert back to string.
	// This will ensure we have a given correct number.
	$number = abrs_decimal( $number )->as_string();

	// Trim the zeros.
	return rtrim( rtrim( $number, '0' ), '.' );
}

/**
 * Sanitizes days of week.
 *
 * @param  mixed $days The input days.
 * @return array|null
 */
function abrs_sanitize_days_of_week( $days ) {
	if ( ! is_array( $days ) ) {
		return;
	}

	$days = array_unique( $days );
	$days_of_week = [ 0, 1, 2, 3, 4, 5, 6 ];

	return array_values( array_intersect( $days_of_week, $days ) );
}

/**
 * Sanitizes a color value with support bold hex & rgba.
 *
 * @param  string $color The value to sanitize.
 * @return string
 */
function abrs_sanitize_color( $color ) {
	if ( empty( $color ) ) {
		return '';
	}

	if ( false !== strpos( $color, '#' ) ) {
		return sanitize_hex_color( $color );
	}

	if ( false !== strpos( $color, 'rgba(' ) ) {
		return abrs_sanitize_rgba_color( $color );
	}

	return '';
}

/**
 * Sanitizes an RGBA color value.
 *
 * @param  string $color The RGBA color value to sanitize.
 * @return string
 */
function abrs_sanitize_rgba_color( $color ) {
	// Trim unneeded whitespace.
	$color = trim( str_replace( ' ', '', $color ) );

	sscanf( $color, 'rgba(%d,%d,%d,%f)', $red, $green, $blue, $alpha );

	if ( ( $red >= 0 && $red <= 255 )
		&& ( $green >= 0 && $green <= 255 )
		&& ( $blue >= 0 && $blue <= 255 )
		&& ( $alpha >= 0 && $alpha <= 1 ) ) {
		return "rgba({$red},{$green},{$blue},{$alpha})";
	}

	return '';
}

/**
 * Sanitizes comma-separated list of IDs.
 *
 * @param  string $list The value to sanitize.
 * @return string
 */
function abrs_sanitize_ids( $list ) {
	return implode( ', ', wp_parse_id_list( $list ) );
}

/**
 * Sanitizes an email.
 *
 * @param  string $email The email to sanitize.
 * @return string
 */
function abrs_sanitize_email( $email ) {
	if ( is_array( $email ) ) {
		$email = implode( ',', $email );
	}

	$email = array_map( 'trim', explode( ',', $email ) );
	$email = array_filter( $email, 'is_email' );

	return implode( ', ', $email );
}
