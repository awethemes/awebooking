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

if ( ! function_exists( 'awebooking_output_content_wrapper' ) ) {

	/**
	 * Output the start of the page wrapper.
	 */
	function awebooking_output_content_wrapper() {
		abrs_get_template( 'global/wrapper-start.php' );
	}
}

if ( ! function_exists( 'awebooking_template_notices' ) ) {
	/**
	 * AweBooking notices template.
	 *
	 * @return void
	 */
	function awebooking_template_notices() {
		abrs_get_template( 'global/notices.php' );
	}
}

if ( ! function_exists( 'awebooking_output_content_wrapper_end' ) ) {

	/**
	 * Output the end of the page wrapper.
	 */
	function awebooking_output_content_wrapper_end() {
		abrs_get_template( 'global/wrapper-end.php' );
	}
}

if ( ! function_exists( 'awebooking_get_sidebar' ) ) {

	/**
	 * Get the room type sidebar template.
	 */
	function awebooking_get_sidebar() {
		abrs_get_template( 'global/sidebar.php' );
	}
}

if ( ! function_exists( 'awebooking_room_type_loop_start' ) ) {

	/**
	 * Output the start of a room type loop. By default this is a UL.
	 *
	 * @param bool $echo echo.
	 * @return string
	 */
	function awebooking_room_type_loop_start( $echo = true ) {
		ob_start();
		abrs_get_template( 'loop/loop-start.php' );
		if ( $echo ) {
			echo ob_get_clean(); // WPCS: xss ok.
		} else {
			return ob_get_clean();
		}
	}
}

if ( ! function_exists( 'awebooking_room_type_loop_end' ) ) {

	/**
	 * Output the end of a room_type loop. By default this is a UL.
	 *
	 * @param bool $echo echo.
	 * @return string
	 */
	function awebooking_room_type_loop_end( $echo = true ) {
		ob_start();

		abrs_get_template( 'loop/loop-end.php' );

		if ( $echo ) {
			echo ob_get_clean(); // WPCS: xss ok.
		} else {
			return ob_get_clean();
		}
	}
}

/**
 * Insert the opening anchor tag for room types in the loop.
 */
function awebooking_template_loop_room_type_link_open() {
	echo '<a href="' . esc_url( get_the_permalink() ) . '">';
}
/**
 * Insert the opening anchor tag for room types in the loop.
 */
function awebooking_template_loop_room_type_link_close() {
	echo '</a>';
}

if ( ! function_exists( 'awebooking_get_room_type_thumbnail' ) ) {

	/**
	 * Get the room type thumbnail, or the placeholder if not set.
	 *
	 * @subpackage Loop
	 * @param string $size (default: 'shop_catalog').
	 * @return string
	 */
	function awebooking_get_room_type_thumbnail( $size = 'awebooking_catalog', $post_id = null ) {
		global $post;
		if ( ! $post_id ) {
			$post_id = $post->ID;
		}

		$image_size = apply_filters( 'single_room_type_archive_thumbnail_size', $size );

		if ( has_post_thumbnail( $post_id ) ) {
			return get_the_post_thumbnail( $post_id, $image_size );
		} elseif ( awebooking_placeholder_img_src() ) {
			return awebooking_placeholder_img( $image_size );
		}
	}
}

if ( ! function_exists( 'awebooking_template_loop_room_type_thumbnail' ) ) {

	/**
	 * Get the room type thumbnail for the loop.
	 *
	 * @subpackage Loop
	 */
	function awebooking_template_loop_room_type_thumbnail() {
		echo awebooking_get_room_type_thumbnail(); // WPCS: xss ok.
	}
}

if ( ! function_exists( 'awebooking_template_loop_room_type_title' ) ) {

	/**
	 * Show the room type title in the room type loop. By default this is an H2.
	 */
	function awebooking_template_loop_room_type_title() {
		the_title( '<h2 class="awebooking-loop-room-type__title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
	}
}

if ( ! function_exists( 'awebooking_template_loop_price' ) ) {

	/**
	 * Get the room type price for the loop.
	 *
	 * @subpackage Loop
	 */
	function awebooking_template_loop_price() {
		abrs_get_template( 'loop/price.php' );
	}
}

if ( ! function_exists( 'awebooking_template_loop_description' ) ) {

	/**
	 * Get the room type description for the loop.
	 *
	 * @subpackage Loop
	 */
	function awebooking_template_loop_description() {
		abrs_get_template( 'loop/description.php' );
	}
}

if ( ! function_exists( 'awebooking_template_loop_view_more' ) ) {

	/**
	 * Get the room type description for the loop.
	 *
	 * @subpackage Loop
	 */
	function awebooking_template_loop_view_more() {
		abrs_get_template( 'loop/view-more.php' );
	}
}

if ( ! function_exists( 'awebooking_template_single_title' ) ) {

	/**
	 * Output the room type title.
	 *
	 * @subpackage Room type
	 */
	function awebooking_template_single_title() {
		abrs_get_template( 'single-room-type/title.php' );
	}
}

if ( ! function_exists( 'awebooking_template_single_price' ) ) {

	/**
	 * Output the room type price.
	 *
	 * @subpackage Room type
	 */
	function awebooking_template_single_price() {
		abrs_get_template( 'single-room-type/price.php' );
	}
}

/**
 * Single Room type.
 */
if ( ! function_exists( 'awebooking_show_room_type_images' ) ) {

	/**
	 * Output the room type image before the single room type summary.
	 *
	 * @subpackage Room type
	 */
	function awebooking_show_room_type_images() {
		abrs_get_template( 'single-room-type/room-type-image.php' );
	}
}

if ( ! function_exists( 'awebooking_show_room_type_thumbnails' ) ) {

	/**
	 * Output the room type thumbnails.
	 *
	 * @subpackage Room type
	 */
	function awebooking_show_room_type_thumbnails() {
		abrs_get_template( 'single-room-type/room-type-thumbnails.php' );
	}
}

if ( ! function_exists( 'awebooking_template_single_title' ) ) {

	/**
	 * Output the room type title.
	 *
	 * @subpackage Room type
	 */
	function awebooking_template_single_title() {
		abrs_get_template( 'single-room-type/title.php' );
	}
}

if ( ! function_exists( 'awebooking_template_single_price' ) ) {

	/**
	 * Output the room type price.
	 *
	 * @subpackage Room type
	 */
	function awebooking_template_single_price() {
		abrs_get_template( 'single-room-type/price.php' );
	}
}
