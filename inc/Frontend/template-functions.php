<?php

/**
 * Display the search rooms form.
 *
 * @param  array   $atts The search form attributes.
 * @param  boolean $echo Is echo or not (return the form).
 * @return string|void
 */
function abrs_get_search_form( $atts = [], $echo = true ) {
	// Pairs the input atts.
	$atts = shortcode_atts([
		'layout' => '',
	], $atts );

	/**
	 * Fires before the search form is retrieved.
	 *
	 * @param array $atts The form attributes.
	 */
	do_action( 'awebooking/pre_get_search_form', $atts );

	$form = abrs_get_template_content( 'search-form.php', compact( 'atts' ) );

	/**
	 * Filters the HTML output of the search form.
	 *
	 * @param string $form The search form HTML output.
	 * @param array  $atts The form attributes.
	 */
	$result = apply_filters( 'awebooking/get_search_form', $form, $atts );

	if ( $echo ) {
		echo $result; // WPCS: XSS OK.
	} else {
		return $result;
	}
}

function abrs_book_room_button( $request = null, $args, $echo = true ) {
	$args = wp_parse_args( $args, [
		'room'        => 0,
		'room_type'   => 0,
		'button_text' => esc_html__( 'Book Now', 'awebooking' ),
		'button_atts' => [],
	]);

	$button = abrs_get_template_content( 'book-button.php', compact( 'args', 'request' ) );

	/**
	 * Filters the HTML output of the search form.
	 *
	 * @param string $form The search form HTML output.
	 * @param array  $atts The form attributes.
	 */
	$button = apply_filters( 'awebooking/book_room_button', $button, $args );

	if ( $echo ) {
		echo $button; // WPCS: XSS OK.
	} else {
		return $button;
	}
}
