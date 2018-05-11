<?php

use AweBooking\Constants;

// Require other functions.
require_once trailingslashit( __DIR__ ) . 'template-functions.php';
require_once trailingslashit( __DIR__ ) . 'hooks.php';

/**
 * Gets the checkout instance.
 *
 * @return \AweBooking\Frontend\Checkout\Checkout
 */
function abrs_checkout() {
	return awebooking()->make( 'checkout' );
}

/**
 * Gets the reservation intance.
 *
 * @return \AweBooking\Reservation\Reservation
 */
function abrs_reservation() {
	return awebooking()->make( 'reservation' );
}

/**
 * Determines if current page is in awebooking pages.
 *
 * @return bool
 */
function is_awebooking() {
	$is_awebooking = (
		   is_room_type_list()
		|| is_room_type()
		|| abrs_is_search_page()
	) ? true : false;

	return apply_filters( 'is_awebooking', $is_awebooking );
}

if ( ! function_exists( 'is_room_type' ) ) {
	/**
	 * Determnies if current viewing in a single room type.
	 *
	 * @return bool
	 */
	function is_room_type() {
		return is_singular( Constants::ROOM_TYPE );
	}
}

if ( ! function_exists( 'is_room_type_list' ) ) {
	/**
	 * Determnies if current page is archive of "room_type".
	 *
	 * @return bool
	 */
	function is_room_type_list() {
		return is_post_type_archive( Constants::ROOM_TYPE );
	}
}

/**
 * Determines if current viewing on "search_results" page.
 *
 * @return bool
 */
function abrs_is_search_page() {
	return is_page( abrs_get_page_id( 'search_results' ) ) || abrs_page_has_shortcode( 'awebooking_search_results' );
}

/**
 * Checks whether the content passed contains a specific short code.
 *
 * @param  string $tag Shortcode tag to check.
 * @return bool
 */
function abrs_page_has_shortcode( $tag = '' ) {
	global $post;

	return is_singular()
		&& ( $post instanceof WP_Post )
		&& has_shortcode( $post->post_content, $tag );
}

/**
 * Add body classes for awebooking pages.
 *
 * @param  array $classes An array of body classes.
 * @return array
 */
function abrs_body_class( $classes ) {
	$classes = (array) $classes;

	if ( is_awebooking() ) {
		$classes[] = 'awebooking-page';
	}

	return array_unique( $classes );
}

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

/**
 * Display the "book now" button.
 *
 * @param  array   $args The args.
 * @param  boolean $echo Is echo or not.
 * @return string|void
 */
function abrs_bookroom_button( $args, $echo = true ) {
	global $wp;

	$args = wp_parse_args( $args, [
		'room_type'   => 0,
		'rate_plan'   => 0,
		'button_text' => esc_html__( 'Book Now', 'awebooking' ),
		'button_atts' => [],
	]);

	$res_request = isset( $wp->query_vars['res_request'] )
		? $wp->query_vars['res_request']
		: null;

	$button = abrs_get_template_content( 'book-button.php', compact( 'args', 'res_request' ) );

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
