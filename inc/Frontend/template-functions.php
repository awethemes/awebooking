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

function abrs_book_room_button( $args, $echo = true ) {
	global $wp;

	$args = wp_parse_args( $args, [
		'room'        => 0,
		'room_type'   => 0,
		'button_text' => esc_html__( 'Book Now', 'awebooking' ),
		'button_atts' => [],
	]);

	$request = $wp->query_vars['res_request'];

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

/**
 * Show the payment methods on the checkout.
 *
 * @access private
 */
function awebooking_checkout_payments() {
	abrs_get_template( 'checkout/payments.php', [
		'checkout'    => abrs_checkout(),
		'gateways'    => awebooking( 'gateways' )->enabled(),
		'button_text' => apply_filters( 'awebooking/booking_button_text', esc_html__( 'Book Now', 'awebooking' ) ),
	]);
}

/**
 * Show the checkout guest controls.
 *
 * @access private
 */
function awebooking_checkout_guest_details() {
	abrs_get_template( 'checkout/form-guest-details.php', [ 'controls' => abrs_checkout()->get_controls() ] );
}

/**
 * Show the checkout additionals controls.
 *
 * @access private
 */
function awebooking_checkout_additionals() {
	abrs_get_template( 'checkout/form-additionals.php', [ 'controls' => abrs_checkout()->get_controls() ] );
}
