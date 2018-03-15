<?php

use AweBooking\Template;
use AweBooking\Constants;
use AweBooking\AweBooking;
use AweBooking\Support\Carbonate;
use AweBooking\Formatting;
use AweBooking\Support\Utils as U;
use AweBooking\Calendar\Period\Period;

/**
 * Get the available container instance.
 *
 * @param  null|string $make Optional, get special binding in the container.
 *                           if null AweBooking instance will be return.
 * @return AweBooking|mixed
 */
function awebooking( $make = null ) {
	if ( is_null( $make ) ) {
		return AweBooking::get_instance();
	}

	return AweBooking::get_instance()->make( $make );
}

/**
 * Get config instance or special setting by key ID.
 *
 * @param  string $key     Special key ID.
 * @param  mixed  $default Default value will be return if key not set,
 *                         if null pass, default setting value will be return.
 * @return mixed
 */
function awebooking_option( $key, $default = null ) {
	return awebooking( 'setting' )->get( $key, $default );
}

/**
 * ------------------------------------------------------
 * AweBooking sanitize functions
 * ------------------------------------------------------
 */

/**
 * Sanitize price number.
 *
 * @param  Price|numeric $number Raw numeric.
 * @return float
 */
function awebooking_sanitize_price( $number ) {
	return Formatting::format_decimal( $number, true );
}

/**
 * Sanitize period.
 *
 * @param  array|mixed $value  Raw date period.
 * @param  bool        $strict Strict validation.
 * @return array
 */
function awebooking_sanitize_period( $value, $strict = false ) {
	$value = (array) $value;
	if ( empty( $value[0] ) || empty( $value[1] ) ) {
		return [];
	}

	try {
		$period = new Period( $value[0], $value[1], $strict );
	} catch ( Exception $e ) {
		return [];
	}

	/*if ( $period->nights() < 1 ) {
		return [];
	}*/

	return [
		$period->get_start_date()->toDateString(),
		$period->get_end_date()->toDateString(),
	];
}

function awebooking_sanitize_standard_date( $value ) {
	return U::rescue( function () use ( $value ) {
		return Carbonate::create_date( $value )->toDateString();
	});
}

/**
 * ------------------------------------------------------
 * Helper functions
 * ------------------------------------------------------
 */

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

/**
 * Return list of common titles.
 *
 * @return string
 */
function awebooking_get_common_titles() {
	return apply_filters( 'awebooking/customer_titles', array(
		'mr'   => esc_html__( 'Mr.', 'awebooking' ),
		'ms'   => esc_html__( 'Ms.', 'awebooking' ),
		'mrs'  => esc_html__( 'Mrs.', 'awebooking' ),
		'miss' => esc_html__( 'Miss.', 'awebooking' ),
		'dr'   => esc_html__( 'Dr.', 'awebooking' ),
		'prof' => esc_html__( 'Prof.', 'awebooking' ),
	));
}

/**
 * ------------------------------------------------------
 * Templates and frontend functions
 * ------------------------------------------------------
 */

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

/**
 * Retrieve page ids.
 *
 * Used for "check_availability", "booking", "checkout".
 *
 * @param  string $page The retrieve page.
 * @return int          Returns -1 if no page is found.
 */
function awebooking_get_page_id( $page ) {
	$page = apply_filters( 'awebooking/get_' . $page . '_page_id', awebooking_option( 'page_' . $page ) );

	return $page ? absint( $page ) : -1;
}

/**
 * Retrieve page permalink.
 *
 * @see awebooking_get_page_id()
 *
 * @param  string $page The retrieve page.
 * @return string
 */
function awebooking_get_page_permalink( $page ) {
	$page_id   = awebooking_get_page_id( $page );
	$permalink = 0 < $page_id ? get_permalink( $page_id ) : get_home_url();

	return apply_filters( 'awebooking/get_' . $page . '_page_permalink', $permalink );
}

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
		return is_post_type_archive( Constants::ROOM_TYPE );
	}
endif;

if ( ! function_exists( 'is_room_type' ) ) :
	/**
	 * Returns true when viewing a single room-type.
	 *
	 * @return bool
	 */
	function is_room_type() {
		return is_singular( Constants::ROOM_TYPE );
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
 * Get an image size.
 *
 * Variable is filtered by awebooking/get_image_size_{image_size}.
 *
 * @param mixed $image_size image size.
 * @return array
 */
function awebooking_get_image_size( $image_size ) {
	if ( is_array( $image_size ) ) {
		$width  = isset( $image_size[0] ) ? $image_size[0] : '300';
		$height = isset( $image_size[1] ) ? $image_size[1] : '300';
		$crop   = isset( $image_size[2] ) ? $image_size[2] : 1;

		$size = array(
			'width'  => $width,
			'height' => $height,
			'crop'   => $crop,
		);

		$image_size = $width . '_' . $height;

	} elseif ( in_array( $image_size, array( 'awebooking_thumbnail', 'awebooking_catalog', 'awebooking_single' ) ) ) {
		$default_size = [];

		$default_size['awebooking_thumbnail'] = array(
			'width'  => '300',
			'height' => '300',
			'crop'   => 1,
		);

		$default_size['awebooking_catalog'] = array(
			'width'  => '600',
			'height' => '400',
			'crop'   => 1,
		);

		$default_size['awebooking_single'] = array(
			'width'  => '640',
			'height' => '640',
			'crop'   => 1,
		);

		$size           = $default_size[ $image_size ];

		$size['width']  = isset( $size['width'] ) ? $size['width'] : '300';
		$size['height'] = isset( $size['height'] ) ? $size['height'] : '300';
		$size['crop']   = isset( $size['crop'] ) ? $size['crop'] : 0;

	} else {
		$size = array(
			'width'  => '300',
			'height' => '300',
			'crop'   => 1,
		);
	}

	return apply_filters( 'awebooking/get_image_size_' . $image_size, $size );
}

/**
 * Get the placeholder image URL for room types etc.
 *
 * @access public
 * @return string
 */
function awebooking_placeholder_img_src() {
	return apply_filters( 'awebooking/placeholder_img_src', awebooking()->plugin_url() . '/assets/img/placeholder.png' );
}

/**
 * Get the placeholder image.
 *
 * @param string $size size.
 * @access public
 * @return string
 */
function awebooking_placeholder_img( $size = 'awebooking_thumbnail' ) {
	$dimensions = awebooking_get_image_size( $size );

	return apply_filters( 'awebooking/placeholder_img', '<img src="' . awebooking_placeholder_img_src() . '" alt="' . esc_attr__( 'Placeholder', 'awebooking' ) . '" width="' . esc_attr( $dimensions['width'] ) . '" class="awebooking-placeholder" height="' . esc_attr( $dimensions['height'] ) . '" />', $size, $dimensions );
}
