<?php
/**
 * A collect of functions for anyone scare OOP.
 *
 * @package AweBooking
 */

use Carbon\Carbon;
use AweBooking\Room;
use AweBooking\AweBooking;
use AweBooking\Support\Date_Utils;
use AweBooking\Support\Formatting;

require_once trailingslashit( __DIR__ ) . '/template-functions.php';
require_once trailingslashit( __DIR__ ) . '/template-hook-functions.php';

/**
 * Get the available container instance.
 *
 * @param  string $make //.
 * @return mixed|AweBooking
 */
function awebooking( $make = null ) {
	if ( is_null( $make ) ) {
		return AweBooking::get_instance();
	}

	return AweBooking::get_instance()->make( $make );
}

/**
 * Get awebooking config instance or special key ID.
 *
 * @param  string $key Optional, special key ID if need.
 * @return mixed
 */
function abkng_config( $key = null ) {
	if ( is_null( $key ) ) {
		return awebooking()->make( 'config' );
	}

	return awebooking( 'config' )->get( $key );
}

/**
 * The datetime should be a string using
 * ISO-8601 "Y-m-d" date format, eg: 2017-05-10.
 *
 * @param string $datetime The date string.
 * @return Carbon
 *
 * @throws InvalidArgumentException //.
 */
function abkng_create_datetime( $datetime ) {
	return Date_Utils::create_date( $datetime );
}

/**
 * Sanitize price number.
 *
 * @param  string|numeric $number Raw numeric.
 * @return float
 */
function abkng_sanitize_price( $number ) {
	return Formatting::format_decimal( $number, true );
}




















/**
 * is_awebooking - Returns true if on a page which uses AweBooking templates (cart and checkout are standard pages with shortcodes and thus are not included).
 * @return bool
 */
function is_awebooking() {
	return apply_filters( 'is_awebooking', ( is_room_type_archive() || is_room_type() || is_check_availability_page() || is_booking_info_page() || is_booking_checkout_page() ) ? true : false );
}

if ( ! function_exists( 'is_room_type_archive' ) ) {

	/**
	 * is_room_type_archive
	 * @return bool
	 */
	function is_room_type_archive() {
		return ( is_post_type_archive( 'room_type' ) );
	}
}

if ( ! function_exists( 'is_room_type' ) ) {

	/**
	 * is_room_type - Returns true when viewing a single room type.
	 * @return bool
	 */
	function is_room_type() {
		return is_singular( array( 'room_type' ) );
	}
}

if ( ! function_exists( 'is_check_availability_page' ) ) {

	/**
	 * is_check_availability_page - Returns true when viewing a single room type.
	 * @return bool
	 */
	function is_check_availability_page() {
		global $wp_query;
		$page_id = $wp_query->get_queried_object_id();

		return ( is_page() && ( intval( abkng_config( 'page_check_availability' ) ) === $page_id ) );
	}
}

if ( ! function_exists( 'is_booking_info_page' ) ) {

	/**
	 * is_check_availability_page - Returns true when viewing a single room type.
	 * @return bool
	 */
	function is_booking_info_page() {
		global $wp_query;
		$page_id = $wp_query->get_queried_object_id();

		return ( is_page() && ( intval( abkng_config( 'page_booking' ) ) === $page_id ) );
	}
}

if ( ! function_exists( 'is_booking_checkout_page' ) ) {

	/**
	 * is_check_availability_page - Returns true when viewing a single room type.
	 * @return bool
	 */
	function is_booking_checkout_page() {
		global $wp_query;
		$page_id = $wp_query->get_queried_object_id();

		return ( is_page() && ( intval( abkng_config( 'page_checkout' ) ) === $page_id ) );
	}
}
