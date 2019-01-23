<?php

use AweBooking\Constants;

// Require other functions.
require_once trailingslashit( __DIR__ ) . 'hooks.php';
require_once trailingslashit( __DIR__ ) . 'template-functions.php';

/**
 * Gets the checkout instance.
 *
 * @return \AweBooking\Checkout\Checkout
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
 * @return \WPLibs\Session\Flash\Flash_Notifier
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
 * Determnies if current viewing in a single hotel.
 *
 * @return bool
 */
function abrs_is_hotel() {
	return is_singular( Constants::HOTEL_LOCATION );
}

/**
 * Determnies if current page is archive of "hotel".
 *
 * @return bool
 */
function abrs_is_hotel_archive() {
	return is_post_type_archive( Constants::HOTEL_LOCATION );
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
	$is_awebooking = ( abrs_is_room_type_archive() || abrs_is_room_type() || abrs_is_checkout_page() || abrs_is_search_page() || abrs_is_hotel() || abrs_is_hotel_archive() );

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
