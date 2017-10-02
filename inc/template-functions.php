<?php

use AweBooking\AweBooking;
use AweBooking\Support\Formatting;

require_once trailingslashit( __DIR__ ) . 'template-hooks.php';

/**
 * Enqueue Scripts
 */
function awebooking_template_scripts() {
	wp_enqueue_style( 'awebooking-template', awebooking()->plugin_url() . '/assets/css/awebooking.css', array(), AweBooking::VERSION );

	wp_enqueue_script( 'jquery-ui-accordion' );
	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_enqueue_script( 'awebooking', awebooking()->plugin_url() . '/assets/js/front-end/awebooking.js', array( 'jquery' ), AweBooking::VERSION, true );

	wp_enqueue_script( 'booking-ajax', awebooking()->plugin_url() . '/assets/js/front-end/booking-handler.js', array( 'jquery' ), AweBooking::VERSION, true );
	wp_localize_script( 'booking-ajax', 'booking_ajax', array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
	));

	global $wp_locale;

	wp_localize_script( 'awebooking', '_awebookingDateSetting', array(
		'i10n' => [
			'locale'        => get_locale(),
			'months'        => array_values( $wp_locale->month ),
			'monthsShort'   => array_values( $wp_locale->month_abbrev ),
			'weekdays'      => array_values( $wp_locale->weekday ),
			'weekdaysMin'   => array_values( $wp_locale->weekday_initial ),
			'weekdaysShort' => array_values( $wp_locale->weekday_abbrev ),
		],
	));
}
add_action( 'wp_enqueue_scripts', 'awebooking_template_scripts', 20 );

if ( ! function_exists( 'awebooking_output_content_wrapper' ) ) {

	/**
	 * Output the start of the page wrapper.
	 */
	function awebooking_output_content_wrapper() {
		awebooking_get_template( 'global/wrapper-start.php' );
	}
}
if ( ! function_exists( 'awebooking_output_content_wrapper_end' ) ) {

	/**
	 * Output the end of the page wrapper.
	 */
	function awebooking_output_content_wrapper_end() {
		awebooking_get_template( 'global/wrapper-end.php' );
	}
}

if ( ! function_exists( 'awebooking_get_sidebar' ) ) {

	/**
	 * Get the room type sidebar template.
	 */
	function awebooking_get_sidebar() {
		awebooking_get_template( 'global/sidebar.php' );
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
		awebooking_get_template( 'loop/loop-start.php' );
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

		awebooking_get_template( 'loop/loop-end.php' );

		if ( $echo ) {
			echo ob_get_clean(); // WPCS: xss ok.
		} else {
			return ob_get_clean();
		}
	}
}


if ( ! function_exists( 'awebooking_location_filter' ) ) {

	/**
	 * Output the room type sorting options.
	 *
	 * @subpackage	Loop
	 */
	function awebooking_location_filter() {

		if ( ! awebooking_option( 'enable_location' ) ) {
			return;
		}

		$locations = get_terms( 'hotel_location', array(
			'hide_empty' => true,
		) );

		$term_default = awebooking( 'config' )->get_default_hotel_location();

		awebooking_get_template( 'loop/location-filter.php', array(
			'locations'   => $locations,
			'term_default' 	=> $term_default,
		) );
	}
}

if ( ! function_exists( 'awebooking_catalog_ordering' ) ) {

	/**
	 * Output the room type sorting options.
	 *
	 * @subpackage	Loop
	 */
	function awebooking_catalog_ordering() {
		awebooking_get_template( 'loop/orderby.php' );
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
	 * @subpackage	Loop
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
	 * @subpackage	Loop
	 */
	function awebooking_template_loop_room_type_thumbnail() {
		echo awebooking_get_room_type_thumbnail(); // WPCS: xss ok.
	}
}

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
	 * @subpackage	Loop
	 */
	function awebooking_template_loop_price() {
		awebooking_get_template( 'loop/price.php' );
	}
}

if ( ! function_exists( 'awebooking_template_loop_description' ) ) {

	/**
	 * Get the room type description for the loop.
	 *
	 * @subpackage	Loop
	 */
	function awebooking_template_loop_description() {
		awebooking_get_template( 'loop/description.php' );
	}
}

if ( ! function_exists( 'awebooking_template_loop_view_more' ) ) {

	/**
	 * Get the room type description for the loop.
	 *
	 * @subpackage	Loop
	 */
	function awebooking_template_loop_view_more() {
		awebooking_get_template( 'loop/view-more.php' );
	}
}

/**
 * Single Room type.
 */
if ( ! function_exists( 'awebooking_show_room_type_images' ) ) {

	/**
	 * Output the room type image before the single room type summary.
	 *
	 * @subpackage	Room type
	 */
	function awebooking_show_room_type_images() {
		awebooking_get_template( 'single-room-type/room-type-image.php' );
	}
}

if ( ! function_exists( 'awebooking_show_room_type_thumbnails' ) ) {

	/**
	 * Output the room type thumbnails.
	 *
	 * @subpackage	Room type
	 */
	function awebooking_show_room_type_thumbnails() {
		awebooking_get_template( 'single-room-type/room-type-thumbnails.php' );
	}
}

if ( ! function_exists( 'awebooking_template_single_title' ) ) {

	/**
	 * Output the room type title.
	 *
	 * @subpackage	Room type
	 */
	function awebooking_template_single_title() {
		awebooking_get_template( 'single-room-type/title.php' );
	}
}

if ( ! function_exists( 'awebooking_template_single_price' ) ) {

	/**
	 * Output the room type price.
	 *
	 * @subpackage	Room type
	 */
	function awebooking_template_single_price() {
		awebooking_get_template( 'single-room-type/price.php' );
	}
}

if ( ! function_exists( 'awebooking_template_single_form' ) ) {

	/**
	 * Output the room type price.
	 *
	 * @subpackage	Room type
	 */
	function awebooking_template_single_form() {
		global $room_type;

		$date_format = Formatting::php_to_js_dateformat(
			awebooking( 'setting' )->get_date_format()
		);

		$max_adults   = awebooking_option( 'check_availability_max_adults' );
		$max_children = awebooking_option( 'check_availability_max_children' );

		$min_night = is_room_type() ? $room_type->get_minimum_night() : 1;

		awebooking_get_template( 'single-room-type/form.php', array(
			'date_format'   => $date_format,
			'max_adults' 	=> $max_adults,
			'max_children' 	=> $max_children,
			'min_night'     => $min_night,
		) );
	}
}

if ( ! function_exists( 'awebooking_output_room_type_data_tabs' ) ) {

	/**
	 * Output the room type tabs.
	 *
	 * @subpackage	Room type/Tabs
	 */
	function awebooking_output_room_type_data_tabs() {
		awebooking_get_template( 'single-room-type/tabs/tabs.php' );
	}
}

if ( ! function_exists( 'awebooking_room_type_description_tab' ) ) {

	/**
	 * Output the description tab content.
	 *
	 * @subpackage	Room type/Tabs
	 */
	function awebooking_room_type_description_tab() {
		awebooking_get_template( 'single-room-type/tabs/description.php' );
	}
}

if ( ! function_exists( 'awebooking_room_type_amenities_tab' ) ) {

	/**
	 * Output the attributes tab content.
	 *
	 * @subpackage	Room type/Tabs
	 */
	function awebooking_room_type_amenities_tab() {
		awebooking_get_template( 'single-room-type/tabs/amenities.php' );
	}
}

if ( ! function_exists( 'awebooking_room_type_extra_services_tab' ) ) {

	/**
	 * Output the attributes tab content.
	 *
	 * @subpackage	Room type/Tabs
	 */
	function awebooking_room_type_extra_services_tab() {
		awebooking_get_template( 'single-room-type/tabs/extra_services.php' );
	}
}

if ( ! function_exists( 'awebooking_default_room_type_tabs' ) ) {

	/**
	 * Add default room type tabs to room type pages.
	 *
	 * @param array $tabs tabs.
	 * @return array
	 */
	function awebooking_default_room_type_tabs( $tabs = array() ) {
		global $room_type, $post;

		// Description tab - shows room type content.
		if ( $post->post_content ) {
			$tabs['description'] = array(
				'title'    => __( 'Description', 'awebooking' ),
				'priority' => 10,
				'callback' => 'awebooking_room_type_description_tab',
			);
		}

		// Optional amenities tab - shows attributes.
		if ( $room_type && $room_type['amenity_ids'] ) {
			$tabs['amenities'] = array(
				'title'    => __( 'Amenities', 'awebooking' ),
				'priority' => 20,
				'callback' => 'awebooking_room_type_amenities_tab',
			);
		}

		// Optional extra services - shows attributes.
		if ( $room_type && $room_type['service_ids'] ) {
			$tabs['extra_services'] = array(
				'title'    => __( 'Extra Services', 'awebooking' ),
				'priority' => 30,
				'callback' => 'awebooking_room_type_extra_services_tab',
			);
		}

		return $tabs;
	}
}

if ( ! function_exists( 'awebooking_pagination' ) ) {
	function awebooking_pagination() {
		awebooking_get_template( 'loop/pagination.php' );
	}
}

/**
 * Outputs a list of room type attributes for a room type.
 *
 * @param  Room_Type $room_type room type obj.
 */
function awebooking_display_room_type_attributes( $room_type ) {
	awebooking_get_template( 'single-room-type/room-type-attributes.php', array(
		'room_type'            => $room_type,
	) );
}

/**
 * Outputs check availability form.
 */
function awebooking_template_check_availability_form() {
	awebooking_get_template( 'check-availability-form.php' );
}

/**
 * Outputs check availability form for input time.
 */
function awebooking_template_check_form_input_time() {
	$date_format = Formatting::php_to_js_dateformat(
		awebooking( 'setting' )->get_date_format()
	);

	awebooking_get_template( 'check-form/input-time.php', array(
		'date_format'   => $date_format,
	) );
}

/**
 * Outputs check availability form for input location.
 */
function awebooking_template_check_form_input_location() {

	$locations = get_terms( 'hotel_location', array(
		'hide_empty' => true,
	) );

	$term_default = awebooking( 'config' )->get_default_hotel_location();

	awebooking_get_template( 'check-form/input-location.php', array(
		'locations'     => $locations,
		'term_default'  => $term_default,
	) );
}

/**
 * Outputs check availability form for input capacity.
 */
function awebooking_template_check_form_input_capacity() {
	$max_adults   = awebooking_option( 'check_availability_max_adults' );
	$max_children = awebooking_option( 'check_availability_max_children' );

	awebooking_get_template( 'check-form/input-capacity.php', array(
		'max_adults' 	=> $max_adults,
		'max_children' 	=> $max_children,
	) );
}

if ( ! function_exists( 'awebooking_template_notices' ) ) :
	/**
	 * AweBooking notices template.
	 *
	 * @return void
	 */
	function awebooking_template_notices() {
		awebooking_get_template( 'global/notices.php' );
	}
endif;

if ( ! function_exists( 'awebooking_template_checkout_customer_form' ) ) :
	/**
	 * AweBooking checkout customer form template.
	 *
	 * @param  object $availability availability.
	 * @return void
	 */
	function awebooking_template_checkout_customer_form() {
		awebooking_get_template( 'checkout/customer-form.php' );
	}
endif;

if ( ! function_exists( 'awebooking_template_checkout_extra_service_details' ) ) :
	/**
	 * AweBooking checkout extra service details template.
	 *
	 * @return void
	 */
	function awebooking_template_checkout_general_informations() {
		awebooking_get_template( 'checkout/general-informations.php' );
	}
endif;
