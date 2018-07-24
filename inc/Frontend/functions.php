<?php

use AweBooking\Constants;

// Require other functions.
require_once trailingslashit( __DIR__ ) . 'hooks.php';
require_once trailingslashit( __DIR__ ) . 'template-functions.php';

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
 * Determnies if current viewing in a single room type.
 *
 * @return bool
 */
function abrs_is_room_type() {
	return is_singular( Constants::ROOM_TYPE );
}

/**
 * Determnies if current page is archive of "room_type".
 *
 * @return bool
 */
function abrs_is_room_type_archive() {
	return is_post_type_archive( Constants::ROOM_TYPE );
}

/**
 * Determines if current viewing on "search_results" page.
 *
 * @return bool
 */
function abrs_is_search_page() {
	$page_id = abrs_get_page_id( 'search_results' );

	return $page_id && is_page( $page_id );
}

/**
 * Determines if current viewing on "checkout" page.
 *
 * @return bool
 */
function abrs_is_checkout_page() {
	$page_id = abrs_get_page_id( 'checkout' );

	return $page_id && is_page( $page_id );
}

/**
 * Determines if current page is in awebooking pages.
 *
 * @return bool
 */
function is_awebooking() {
	$is_awebooking = ( abrs_is_room_type_archive() || abrs_is_room_type() || abrs_is_checkout_page() || abrs_is_search_page() );

	return apply_filters( 'is_awebooking', $is_awebooking );
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
		$classes[] = 'awebooking';
	}

	if ( abrs_is_search_page() ) {
		$classes[] = 'awebooking-check-availability';
	}

	if ( abrs_is_checkout_page() ) {
		$classes[] = 'awebooking-checkout';
	}

	return array_unique( $classes );
}

/**
 * Display the search rooms form.
 *
 * @param  array   $atts The search form attributes.
 * @param  boolean $echo Is echo or not (return the form).
 * @return string
 */
function abrs_get_search_form( $atts = [], $echo = true ) {
	global $abrs_query;

	// Pairs the input atts.
	$atts = shortcode_atts([
		'layout'          => 'horizontal',
		'alignment'       => '',
		'res_request'     => null,
		'hotel_location'  => true,
		'occupancy'       => true,
		'only_room'       => null,
		'container_class' => '',
	], $atts );

	/**
	 * Fires before the search form is retrieved.
	 *
	 * @param array $atts The form attributes.
	 */
	do_action( 'abrs_pre_get_search_form', $atts );

	if ( $abrs_query->res_request && is_null( $atts['res_request'] ) && $abrs_query ) {
		$res_request = $abrs_query->res_request;
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
 * @return string
 */
function abrs_book_room_button( $args, $echo = true ) {
	global $abrs_query;

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

/**
 * Get the room type thumbnail, or the placeholder if not set.
 *
 * @param int|null $post_id The post ID.
 * @param string   $size    The image size (default: 'awebooking_archive').
 *
 * @return string
 */
function abrs_get_thumbnail( $post_id = null, $size = 'awebooking_archive' ) {
	global $post;

	if ( ! $post_id ) {
		$post_id = $post->ID;
	}

	if ( ! has_post_thumbnail( $post_id ) ) {
		return '';
	}

	return get_the_post_thumbnail( $post_id, $size );
}
