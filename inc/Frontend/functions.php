<?php

use AweBooking\Constants;
use AweBooking\Availability\Request;
use Illuminate\Support\Arr;

// Require other functions.
require_once trailingslashit( __DIR__ ) . 'template-functions.php';
require_once trailingslashit( __DIR__ ) . 'hooks.php';

/**
 * Add a notice message to the flash.
 *
 * @param  string $message The notice message.
 * @param  string $level   The notice level.
 * @return \AweBooking\Component\Flash\Flash_Notifier
 */
function abrs_add_notice( $message, $level = 'info' ) {
	return abrs_flash( $message, $level );
}

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
 * Gets the current res request.
 *
 * @return \AweBooking\Availability\Request|null
 */
function abrs_get_res_request() {
	global $wp;

	return Arr::get( $wp->query_vars, 'res_request' );
}

/**
 * Sets the res request into the query var.
 *
 * @param \AweBooking\Availability\Request|null $request The res request instance.
 */
function abrs_set_res_request( Request $request = null ) {
	global $wp;

	$wp->set_query_var( 'res_request', $request );
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
 * Determines if current viewing on "checkout" page.
 *
 * @return bool
 */
function abrs_is_checkout_page() {
	return is_page( abrs_get_page_id( 'checkout' ) ) || abrs_page_has_shortcode( 'awebooking_checkout' );
}









/**
 * Determines if current page is in awebooking pages.
 *
 * @return bool
 */
function is_awebooking() {
	$is_awebooking = ( is_room_type_list() || is_room_type() || abrs_is_checkout_page() || abrs_is_search_page() ) ? true : false;

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
	global $wp;

	// Pairs the input atts.
	$atts = shortcode_atts([
		'layout'          => 'horizontal',
		'alignment'       => '',
		'container_class' => '',
		'res_request'     => null,
		'hotel_location'  => true,
	], $atts );

	/**
	 * Fires before the search form is retrieved.
	 *
	 * @param array $atts The form attributes.
	 */
	do_action( 'abrs_pre_get_search_form', $atts );

	if ( is_null( $atts['res_request'] ) && ! empty( $wp->query_vars['res_request'] ) ) {
		$res_request = $wp->query_vars['res_request'];
	} else {
		$res_request = abrs_create_res_request([
			'check_in'  => 'today',
			'check_out' => 'tomorrow',
		]);
	}

	if ( is_null( $res_request ) || is_wp_error( $res_request ) ) {
		$res_request = null;
	}

	$form = abrs_get_template_content( 'search-form.php', compact( 'atts', 'res_request' ) );

	/**
	 * Filters the HTML output of the search form.
	 *
	 * @param string $form The search form HTML output.
	 * @param array  $atts The form attributes.
	 */
	$result = apply_filters( 'abrs_get_search_form', $form, $atts );

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
	global $wp, $abrs_query;

	$args = wp_parse_args( $args, [
		'room_type'   => 0,
		'rate_plan'   => 0,
		'show_button' => true,
		'button_text' => esc_html__( 'Book Now', 'awebooking' ),
		'button_atts' => [],
	]);

	$res_request = ( $abrs_query && $abrs_query->res_request )
		? $abrs_query->res_request
		: null;

	$button = abrs_get_template_content( 'book-button.php', compact( 'args', 'res_request' ) );

	/**
	 * Filters the HTML output of the search form.
	 *
	 * @param string $form The search form HTML output.
	 * @param array  $atts The form attributes.
	 */
	$button = apply_filters( 'abrs_book_room_button', $button, $args );

	if ( $echo ) {
		echo $button; // WPCS: XSS OK.
	} else {
		return $button;
	}
}
