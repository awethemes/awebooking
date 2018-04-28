<?php

use AweBooking\Constants;

// Require other functions.
require_once trailingslashit( __DIR__ ) . 'template-functions.php';
require_once trailingslashit( __DIR__ ) . 'hooks.php';

/**
 * Determines if current page is in awebooking pages.
 *
 * @return bool
 */
function is_awebooking() {
	$is_awebooking = (
		   is_room_type_list()
		|| is_room_type()
		|| is_awebooking_search()
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

if ( ! function_exists( 'is_awebooking_search' ) ) {
	/**
	 * Returns true when viewing a "search results" page.
	 *
	 * @return bool
	 */
	function is_awebooking_search() {
		return is_page( abrs_get_page_id( 'cart' ) ) || abrs_page_has_shortcode( 'awebooking_search_results' );
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
