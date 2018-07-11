<?php
/**
 * Functions for the templates.
 *
 * @package AweBooking
 */

/**
 * Prints messages and errors which are stored in the flash.
 *
 * @return void
 */
function abrs_print_notices() {
	abrs_get_template( 'notices.php', [ 'messages' => abrs_flash()->all() ] );
}

/**
 * Display the search form on the search page.
 *
 * @return void
 */
function abrs_search_form_on_search() {
	abrs_get_template( 'search/search-form.php' );
}

/**
 * Display the filter form on the search page.
 *
 * @return void
 */
function abrs_filter_form() {
	abrs_get_template( 'search/filter-form.php' );
}

/**
 * Display search result item.
 *
 * @param  \AweBooking\Availability\Request   $res_request    The current reservation request.
 * @param  \AweBooking\Model\Room_Type        $room_type      The room type instance.
 * @param  \AweBooking\Availability\Room_Rate $room_rate      The room rate instance.
 * @param  array                              $availabilities An array of availabilities.
 *
 * @return void
 * @access private
 */
function abrs_search_result_item( $res_request, $room_type, $room_rate, $availabilities ) {
	abrs_get_template( 'search/result-item.php', get_defined_vars() );
}

/**
 * Show the checkout services.
 *
 * @access private
 */
function abrs_checkout_services() {
	abrs_get_template( 'checkout/services.php', [
		'services' => abrs_list_services(),
		'includes' => abrs_reservation()->get_included_services(),
	]);
}

/**
 * Show the checkout guest controls.
 *
 * @access private
 */
function abrs_checkout_guest_details() {
	abrs_get_template( 'checkout/form-guest-details.php', [ 'controls' => abrs_checkout()->get_controls() ] );
}

/**
 * Show the checkout additionals controls.
 *
 * @access private
 */
function abrs_checkout_additionals() {
	abrs_get_template( 'checkout/form-additionals.php', [ 'controls' => abrs_checkout()->get_controls() ] );
}

/**
 * Show the payment methods on the checkout.
 *
 * @access private
 */
function abrs_checkout_payments() {
	abrs_get_template( 'checkout/payments.php', [
		'checkout'    => abrs_checkout(),
		'gateways'    => awebooking( 'gateways' )->enabled(),
		'button_text' => apply_filters( 'abrs_booking_button_text', esc_html__( 'Book Now', 'awebooking' ) ),
	]);
}

if ( ! function_exists( 'abrs_content_wrapper_before' ) ) {

	/**
	 * Output the start of the page wrapper.
	 *
	 * @access private
	 */
	function abrs_content_wrapper_before() {
		abrs_get_template( 'template-parts/global/wrapper-start.php' );
	}
}

if ( ! function_exists( 'abrs_content_wrapper_after' ) ) {

	/**
	 * Output the end of the page wrapper.
	 *
	 * @access private
	 */
	function abrs_content_wrapper_after() {
		abrs_get_template( 'template-parts/global/wrapper-end.php' );
	}
}

if ( ! function_exists( 'abrs_get_thumbnail' ) ) {

	/**
	 * Get the room type thumbnail, or the placeholder if not set.
	 *
	 * @param string $size (default: 'awebooking_archive').
	 * @return string
	 */
	function abrs_get_thumbnail( $post_id = null, $size = 'awebooking_archive' ) {
		global $post;
		if ( ! $post_id ) {
			$post_id = $post->ID;
		}

		if ( ! has_post_thumbnail( $post_id ) ) {
			return;
		}

		return get_the_post_thumbnail( $post_id, $size );
	}
}

if ( ! function_exists( 'abrs_search_result_header' ) ) {

	/**
	 * Gets search result room list.
	 */
	function abrs_search_result_header( $room_type, $room_rate ) {
		abrs_get_template( 'search/result/header.php', compact( 'room_type', 'room_rate' ) );
	}
}

/* Search result */
if ( ! function_exists( 'abrs_search_result_room_type' ) ) {

	/**
	 * Gets search result room type.
	 */
	function abrs_search_result_room_type( $room_type, $room_rate ) {
		abrs_get_template( 'search/result/room-type.php', compact( 'room_type', 'room_rate' ) );
	}
}

if ( ! function_exists( 'abrs_search_result_room_list' ) ) {

	/**
	 * Gets search result room list.
	 */
	function abrs_search_result_room_list( $room_type, $room_rate ) {
		abrs_get_template( 'search/result/room-list.php', compact( 'room_type', 'room_rate' ) );
	}
}

if ( ! function_exists( 'abrs_search_result_room_price' ) ) {

	/**
	 * Gets search result room list.
	 */
	function abrs_search_result_room_price( $room_type, $room_rate ) {
		abrs_get_template( 'search/result/price.php', compact( 'room_type', 'room_rate' ) );
	}
}

/* Single room */
if ( ! function_exists( 'abrs_single_room_description' ) ) {

	/**
	 * Gets single room description.
	 */
	function abrs_single_room_description() {
		abrs_get_template_part( 'template-parts/single/description' );
	}
}

if ( ! function_exists( 'abrs_single_room_amenities' ) ) {

	/**
	 * Gets single room amenities.
	 */
	function abrs_single_room_amenities() {
		abrs_get_template_part( 'template-parts/single/amenities' );
	}
}

if ( ! function_exists( 'abrs_single_room_gallery' ) ) {

	/**
	 * Gets single room gallery.
	 */
	function abrs_single_room_gallery() {
		abrs_get_template_part( 'template-parts/single/gallery' );
	}
}

if ( ! function_exists( 'abrs_single_room_form' ) ) {

	/**
	 * Gets single room form.
	 */
	function abrs_single_room_form() {
		abrs_get_template_part( 'template-parts/single/form' );
	}
}

/* Archive room */
if ( ! function_exists( 'abrs_pagination' ) ) {

	/**
	 * Gets archive room thumbnail.
	 */
	function abrs_pagination() {
		abrs_get_template_part( 'template-parts/archive/pagination' );
	}
}

if ( ! function_exists( 'abrs_archive_room_loop_start' ) ) {

	/**
	 * Output the start of a room type loop.
	 *
	 * @param bool $echo echo.
	 * @return string
	 */
	function abrs_archive_room_loop_start( $echo = true ) {
		ob_start();
		abrs_get_template( 'template-parts/archive/loop-start.php' );
		if ( $echo ) {
			echo ob_get_clean(); // WPCS: xss ok.
		} else {
			return ob_get_clean();
		}
	}
}

if ( ! function_exists( 'abrs_archive_room_loop_end' ) ) {

	/**
	 * Output the end of a room_type loop.
	 *
	 * @param bool $echo echo.
	 * @return string
	 */
	function abrs_archive_room_loop_end( $echo = true ) {
		ob_start();

		abrs_get_template( 'template-parts/archive/loop-end.php' );

		if ( $echo ) {
			echo ob_get_clean(); // WPCS: xss ok.
		} else {
			return ob_get_clean();
		}
	}
}

if ( ! function_exists( 'abrs_no_rooms_found' ) ) {

	/**
	 * No rooms found.
	 */
	function abrs_no_rooms_found() {
		abrs_get_template_part( 'template-parts/archive/content', 'none' );
	}
}

if ( ! function_exists( 'abrs_archive_room_thumbnail' ) ) {

	/**
	 * Gets archive room thumbnail.
	 */
	function abrs_archive_room_thumbnail() {
		echo abrs_get_thumbnail(); // WPCS: xss ok.
	}
}

if ( ! function_exists( 'abrs_archive_room_title' ) ) {

	/**
	 * Gets archive room title.
	 */
	function abrs_archive_room_title() {
		abrs_get_template_part( 'template-parts/archive/title' );
	}
}

if ( ! function_exists( 'abrs_archive_room_price' ) ) {

	/**
	 * Gets archive room price.
	 */
	function abrs_archive_room_price() {
		abrs_get_template_part( 'template-parts/archive/price' );
	}
}

if ( ! function_exists( 'abrs_archive_room_description' ) ) {

	/**
	 * Gets archive room description.
	 */
	function abrs_archive_room_description() {
		abrs_get_template_part( 'template-parts/archive/description' );
	}
}

if ( ! function_exists( 'abrs_archive_room_information' ) ) {

	/**
	 * Gets archive room information.
	 */
	function abrs_archive_room_information() {
		abrs_get_template_part( 'template-parts/archive/information' );
	}
}

if ( ! function_exists( 'abrs_archive_room_occupancy' ) ) {

	/**
	 * Gets archive room occupancy.
	 */
	function abrs_archive_room_occupancy() {
		abrs_get_template_part( 'template-parts/archive/occupancy' );
	}
}

if ( ! function_exists( 'abrs_archive_room_button' ) ) {

	/**
	 * Gets archive room button.
	 */
	function abrs_archive_room_button() {
		abrs_get_template_part( 'template-parts/archive/button' );
	}
}
