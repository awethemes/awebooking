<?php


if ( ! function_exists( 'is_awebooking' ) ) :
	/**
	 * Returns true if on a page which uses AweBooking templates.
	 *
	 * @return bool
	 */
	function is_awebooking() {
		$is_awebooking = (
			is_room_type_archive() || is_room_type() ||
			is_check_availability_page() || is_booking_info_page() || is_booking_checkout_page()
		) ? true : false;
		return apply_filters( 'is_awebooking', $is_awebooking );
	}
endif;

if ( ! function_exists( 'is_room_type_archive' ) ) :
	/**
	 * Is current page is archive of "room_type".
	 *
	 * @return bool
	 */
	function is_room_type_archive() {
		return is_post_type_archive( AweBooking::ROOM_TYPE );
	}
endif;

if ( ! function_exists( 'is_room_type' ) ) :
	/**
	 * Returns true when viewing a single room-type.
	 *
	 * @return bool
	 */
	function is_room_type() {
		return is_singular( AweBooking::ROOM_TYPE );
	}
endif;

if ( ! function_exists( 'is_check_availability_page' ) ) :
	/**
	 * Returns true when viewing a "search availability results " page.
	 *
	 * @return bool
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
	 * @return bool
	 */
	function is_booking_info_page() {
		global $wp_query;
		$current_id = $wp_query->get_queried_object_id();
		$page_id = (int) awebooking( 'config' )->get( 'page_booking' );
		return ( is_page() && $current_id === $page_id );
	}
endif;

if ( ! function_exists( 'is_booking_checkout_page' ) ) :
	/**
	 * Returns true when viewing a "booking checkout" page.
	 *
	 * @return bool
	 */
	function is_booking_checkout_page() {
		global $wp_query;
		$current_id = $wp_query->get_queried_object_id();
		$page_id = (int) awebooking( 'config' )->get( 'page_checkout' );
		return ( is_page() && $current_id === $page_id );
	}
endif;


/**
 * Locate a template and return the path for inclusion.
 *
 * @param  string $template_name Template name.
 * @return string
 */
function awebooking_locate_template( $template_name ) {
	return Template::locate_template( $template_name );
}

/**
 * Include a template by given a template name.
 *
 * @param string $template_name Template name.
 * @param array  $args          Template arguments.
 */
function awebooking_get_template( $template_name, $args = array() ) {
	Template::get_template( $template_name, $args );
}

/**
 * Load a template part into a template.
 *
 * @param string $slug The slug name for the generic template.
 * @param string $name The name of the specialised template.
 */
function awebooking_get_template_part( $slug, $name = '' ) {
	Template::get_template_part( $slug, $name );
}
