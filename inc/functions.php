<?php
/**
 * A collect of functions for anyone scare OOP.
 *
 * @package AweBooking
 */

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
 * Returns true if on a page which uses AweBooking templates.
 *
 * @return boolean
 */
function is_awebooking() {
	$is_awebooking = (
		is_room_type_archive() || is_room_type() ||
		is_check_availability_page() || is_booking_info_page() || is_booking_checkout_page()
	) ? true : false;

	return apply_filters( 'is_awebooking', $is_awebooking );
}

if ( ! function_exists( 'is_room_type_archive' ) ) :
	/**
	 * Is current page is archive of "room_type".
	 *
	 * @return boolean
	 */
	function is_room_type_archive() {
		return is_post_type_archive( AweBooking::ROOM_TYPE );
	}
endif;

if ( ! function_exists( 'is_room_type' ) ) :
	/**
	 * Returns true when viewing a single room-type.
	 *
	 * @return boolean
	 */
	function is_room_type() {
		return is_singular( AweBooking::ROOM_TYPE );
	}
endif;

if ( ! function_exists( 'is_check_availability_page' ) ) :
	/**
	 * Returns true when viewing a "search availability results " page.
	 *
	 * @return boolean
	 */
	function is_check_availability_page() {
		global $wp_query;

		$current_id = $wp_query->get_queried_object_id();
		$page_id = (int) awebooking( 'config' )->get( 'page_check_availability' );

		return ( is_page() && $current_id === $page_id );
	}
endif;

if ( ! function_exists( 'is_booking_info_page' ) ) :
	/**
	 * Returns true when viewing a "booking review" page.
	 *
	 * @return boolean
	 */
	function is_booking_info_page() {
		global $wp_query;

		$current_id = $wp_query->get_queried_object_id();
		$page_id = (int) awebooking( 'config' )->get( 'page_booking' );

		return ( is_page() && $current_id === $page_id );
	}
endif;

if ( ! function_exists( 'is_booking_checkout_page' ) ) {
	/**
	 * Returns true when viewing a "booking checkout" page.
	 *
	 * @return boolean
	 */
	function is_booking_checkout_page() {
		global $wp_query;

		$current_id = $wp_query->get_queried_object_id();
		$page_id = (int) awebooking( 'config' )->get( 'page_checkout' );

		return ( is_page() && $current_id === $page_id );
	}
}

if ( ! function_exists( 'wp_data_callback' ) ) :
	/**
	 * Get Wordpress specific data from the DB and return in a usable array.
	 *
	 * @param  string $type Data type.
	 * @param  mixed  $args Optional, data query args or something else.
	 * @return array
	 */
	function wp_data_callback( $type, $args = array() ) {
		return function() use ( $type, $args ) {
			return Skeleton\Support\WP_Data::get( $type, $args );
		};
	}
endif;

if ( ! function_exists( 'priority_list' ) ) :
	/**
	 * Make a list stack
	 *
	 * @param  array $values An array values.
	 * @return static
	 */
	function priority_list( array $values ) {
		$stack = new Skeleton\Support\Priority_List;

		foreach ( $values as $key => $value ) {
			$priority = is_object( $value ) ? $value->priority : $value['priority'];
			$stack->insert( $key, $value, $priority );
		}

		return $stack;
	}
endif;

/**
 * Sanitize price number.
 *
 * @param  string|numeric $number Raw numeric.
 * @return float
 */
function abkng_sanitize_price( $number ) {
	return AweBooking\Support\Formatting::format_decimal( $number, true );
}
