<?php
/**
 * Room Type Template
 *
 * Functions for the templating system.
 *
 * @author   Awethemes
 * @category Core
 * @package  Awethemes/Functions
 * @version  1.0.0
 */

use AweBooking\Support\Utils;
use AweBooking\Support\Formatting;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Enqueue Scripts
 */
function abkng_template_scripts() {
	wp_enqueue_style( 'awebooking-template', AweBooking()->plugin_url() . '/assets/css/awebooking.css', array(), AweBooking::VERSION );

	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_enqueue_script( 'awebooking', AweBooking()->plugin_url() . '/assets/js/front-end/awebooking.js', array( 'jquery' ), AweBooking::VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'abkng_template_scripts', 20 );

/**
 * Get template part.
 *
 * @param mixed  $slug slug.
 * @param string $name (default: '').
 */
function abkng_get_template_part( $slug, $name = '' ) {
	$template = '';

	// Look in yourtheme/slug-name.php and yourtheme/awebooking/slug-name.php .
	if ( $name ) {
		$template = locate_template( array( "{$slug}-{$name}.php", AweBooking()->template_path() . "{$slug}-{$name}.php" ) );
	}

	// Get default slug-name.php .
	if ( ! $template && $name && file_exists( AweBooking()->plugin_path() . "/templates/{$slug}-{$name}.php" ) ) {
		$template = AweBooking()->plugin_path() . "/templates/{$slug}-{$name}.php";
	}

	// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/awebooking/slug.php .
	if ( ! $template ) {
		$template = locate_template( array( "{$slug}.php", AweBooking()->template_path() . "{$slug}.php" ) );
	}

	// Allow 3rd party plugins to filter template file from their plugin.
	$template = apply_filters( 'abkng_get_template_part', $template, $slug, $name );

	if ( $template ) {
		load_template( $template, false );
	}
}

/**
 * Get other templates (e.g. room_type attributes) passing attributes and including the file.
 *
 * @access public
 * @param string $template_name template name .
 * @param array  $args (default: array()).
 * @param string $template_path (default: '').
 * @param string $default_path (default: '').
 */
function abkng_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	if ( ! empty( $args ) && is_array( $args ) ) {
		extract( $args );
	}

	$located = abkng_locate_template( $template_name, $template_path, $default_path );

	if ( ! file_exists( $located ) ) { // WPCS: xss ok.
		_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '1.0.0' );// WPCS: xss ok.
		return;
	}

	// Allow 3rd party plugin filter template file from their plugin.
	$located = apply_filters( 'awebooking/get_template', $located, $template_name, $args, $template_path, $default_path );

	do_action( 'awebooking/before_template_part', $template_name, $template_path, $located, $args );

	include( $located );

	do_action( 'awebooking/after_template_part', $template_name, $template_path, $located, $args );
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 *		yourtheme		/	$template_path	/	$template_name
 *		yourtheme		/	$template_name
 *		$default_path	/	$template_name
 *
 * @access public
 * @param string $template_name template name.
 * @param string $template_path (default: '').
 * @param string $default_path (default: '').
 * @return string
 */
function abkng_locate_template( $template_name, $template_path = '', $default_path = '' ) {

	// Look within passed path within the theme - this is priority.
	$template = locate_template( [
		trailingslashit( awebooking()->template_path() ) . $template_name,
		$template_name,
	]);

	// Get default template/.
	if ( ! $template ) {
		$template =  awebooking()->plugin_path() . '/templates/' . $template_name;
	}

	// Return what we found.
	return apply_filters( 'awebooking/locate_template', $template, $template_name, $template_path );
}

/**
 * Global
 */
if ( ! function_exists( 'abkng_output_content_wrapper' ) ) {

	/**
	 * Output the start of the page wrapper.
	 */
	function abkng_output_content_wrapper() {
		abkng_get_template( 'global/wrapper-start.php' );
	}
}
if ( ! function_exists( 'abkng_output_content_wrapper_end' ) ) {

	/**
	 * Output the end of the page wrapper.
	 */
	function abkng_output_content_wrapper_end() {
		abkng_get_template( 'global/wrapper-end.php' );
	}
}

if ( ! function_exists( 'abkng_get_sidebar' ) ) {

	/**
	 * Get the room type sidebar template.
	 */
	function abkng_get_sidebar() {
		abkng_get_template( 'global/sidebar.php' );
	}
}

if ( ! function_exists( 'abkng_room_type_loop_start' ) ) {

	/**
	 * Output the start of a room type loop. By default this is a UL.
	 *
	 * @param bool $echo echo.
	 * @return string
	 */
	function abkng_room_type_loop_start( $echo = true ) {
		ob_start();
		abkng_get_template( 'loop/loop-start.php' );
		if ( $echo ) {
			echo ob_get_clean(); // WPCS: xss ok.
		} else {
			return ob_get_clean();
		}
	}
}

if ( ! function_exists( 'abkng_room_type_loop_end' ) ) {

	/**
	 * Output the end of a room_type loop. By default this is a UL.
	 *
	 * @param bool $echo echo.
	 * @return string
	 */
	function abkng_room_type_loop_end( $echo = true ) {
		ob_start();

		abkng_get_template( 'loop/loop-end.php' );

		if ( $echo ) {
			echo ob_get_clean(); // WPCS: xss ok.
		} else {
			return ob_get_clean();
		}
	}
}


if ( ! function_exists( 'abkng_location_filter' ) ) {

	/**
	 * Output the room type sorting options.
	 *
	 * @subpackage	Loop
	 */
	function abkng_location_filter() {

		if ( ! abkng_config( 'enable_location' ) ) {
			return;
		}

		$locations = get_terms( 'hotel_location', array(
			'hide_empty' => true,
		) );

		$term_default = Utils::get_hotel_location_default();

		abkng_get_template( 'loop/location-filter.php', array(
			'locations'   => $locations,
			'term_default' 	=> $term_default,
		) );
	}
}

if ( ! function_exists( 'abkng_catalog_ordering' ) ) {

	/**
	 * Output the room type sorting options.
	 *
	 * @subpackage	Loop
	 */
	function abkng_catalog_ordering() {
		abkng_get_template( 'loop/orderby.php' );
	}
}

/**
 * Insert the opening anchor tag for room types in the loop.
 */
function abkng_template_loop_room_type_link_open() {
	echo '<a href="' . esc_url( get_the_permalink() ) . '">';
}
/**
 * Insert the opening anchor tag for room types in the loop.
 */
function abkng_template_loop_room_type_link_close() {
	echo '</a>';
}

if ( ! function_exists( 'abkng_get_room_type_thumbnail' ) ) {

	/**
	 * Get the room type thumbnail, or the placeholder if not set.
	 *
	 * @subpackage	Loop
	 * @param string $size (default: 'shop_catalog').
	 * @return string
	 */
	function abkng_get_room_type_thumbnail( $size = 'awebooking_catalog' ) {
		global $post;
		$image_size = apply_filters( 'single_room_type_archive_thumbnail_size', $size );

		if ( has_post_thumbnail() ) {
			return get_the_post_thumbnail( $post->ID, $image_size );
		} elseif ( abkng_placeholder_img_src() ) {
			return abkng_placeholder_img( $image_size );
		}
	}
}

if ( ! function_exists( 'abkng_template_loop_room_type_thumbnail' ) ) {

	/**
	 * Get the room type thumbnail for the loop.
	 *
	 * @subpackage	Loop
	 */
	function abkng_template_loop_room_type_thumbnail() {
		echo abkng_get_room_type_thumbnail(); // WPCS: xss ok.
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
function abkng_get_image_size( $image_size ) {
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
function abkng_placeholder_img_src() {
	return apply_filters( 'awebooking/placeholder_img_src', Awebooking()->plugin_url() . '/assets/img/placeholder.png' );
}

/**
 * Get the placeholder image.
 *
 * @param string $size size.
 * @access public
 * @return string
 */
function abkng_placeholder_img( $size = 'awebooking_thumbnail' ) {
	$dimensions = abkng_get_image_size( $size );

	return apply_filters( 'awebooking/placeholder_img', '<img src="' . abkng_placeholder_img_src() . '" alt="' . esc_attr__( 'Placeholder', 'awebooking' ) . '" width="' . esc_attr( $dimensions['width'] ) . '" class="awebooking-placeholder" height="' . esc_attr( $dimensions['height'] ) . '" />', $size, $dimensions );
}

if ( ! function_exists( 'abkng_template_loop_room_type_title' ) ) {

	/**
	 * Show the room type title in the room type loop. By default this is an H2.
	 */
	function abkng_template_loop_room_type_title() {
		the_title( '<h2 class="awebooking-loop-room-type__title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
	}
}

if ( ! function_exists( 'abkng_template_loop_price' ) ) {

	/**
	 * Get the room type price for the loop.
	 *
	 * @subpackage	Loop
	 */
	function abkng_template_loop_price() {
		abkng_get_template( 'loop/price.php' );
	}
}

if ( ! function_exists( 'abkng_template_loop_description' ) ) {

	/**
	 * Get the room type description for the loop.
	 *
	 * @subpackage	Loop
	 */
	function abkng_template_loop_description() {
		abkng_get_template( 'loop/description.php' );
	}
}

if ( ! function_exists( 'abkng_template_loop_view_more' ) ) {

	/**
	 * Get the room type description for the loop.
	 *
	 * @subpackage	Loop
	 */
	function abkng_template_loop_view_more() {
		abkng_get_template( 'loop/view-more.php' );
	}
}

/**
 * Single Room type.
 */
if ( ! function_exists( 'abkng_show_room_type_images' ) ) {

	/**
	 * Output the room type image before the single room type summary.
	 *
	 * @subpackage	Room type
	 */
	function abkng_show_room_type_images() {
		abkng_get_template( 'single-room-type/room-type-image.php' );
	}
}

if ( ! function_exists( 'abkng_show_room_type_thumbnails' ) ) {

	/**
	 * Output the room type thumbnails.
	 *
	 * @subpackage	Room type
	 */
	function abkng_show_room_type_thumbnails() {
		abkng_get_template( 'single-room-type/room-type-thumbnails.php' );
	}
}

if ( ! function_exists( 'abkng_output_room_type_data_tabs' ) ) {

	/**
	 * Output the room type tabs.
	 *
	 * @subpackage	Room type/Tabs
	 */
	function abkng_output_room_type_data_tabs() {
		abkng_get_template( 'single-room-type/tabs/tabs.php' );
	}
}

if ( ! function_exists( 'abkng_template_single_title' ) ) {

	/**
	 * Output the room type title.
	 *
	 * @subpackage	Room type
	 */
	function abkng_template_single_title() {
		abkng_get_template( 'single-room-type/title.php' );
	}
}

if ( ! function_exists( 'abkng_template_single_price' ) ) {

	/**
	 * Output the room type price.
	 *
	 * @subpackage	Room type
	 */
	function abkng_template_single_price() {
		abkng_get_template( 'single-room-type/price.php' );
	}
}

if ( ! function_exists( 'abkng_template_single_form' ) ) {

	/**
	 * Output the room type price.
	 *
	 * @subpackage	Room type
	 */
	function abkng_template_single_form() {
		global $room_type;
		$date_format  = abkng_config( 'date_format' );
		$date_format = Formatting::php_to_js_dateformat( $date_format ); // TODO: error "F"
		$max_adults   = abkng_config( 'check_availability_max_adults' );
		$max_children = abkng_config( 'check_availability_max_children' );
		$min_night = is_room_type() ? $room_type->get_minimum_night() : 1;

		abkng_get_template( 'single-room-type/form.php', array(
			'date_format'   => $date_format,
			'max_adults' 	=> $max_adults,
			'max_children' 	=> $max_children,
			'min_night'     => $min_night,
		) );
	}
}

if ( ! function_exists( 'abkng_output_room_type_data_tabs' ) ) {

	/**
	 * Output the room type tabs.
	 *
	 * @subpackage	Room type/Tabs
	 */
	function abkng_output_room_type_data_tabs() {
		abkng_get_template( 'single-room-type/tabs/tabs.php' );
	}
}

if ( ! function_exists( 'abkng_room_type_description_tab' ) ) {

	/**
	 * Output the description tab content.
	 *
	 * @subpackage	Room type/Tabs
	 */
	function abkng_room_type_description_tab() {
		abkng_get_template( 'single-room-type/tabs/description.php' );
	}
}

if ( ! function_exists( 'abkng_room_type_amenities_tab' ) ) {

	/**
	 * Output the attributes tab content.
	 *
	 * @subpackage	Room type/Tabs
	 */
	function abkng_room_type_amenities_tab() {
		abkng_get_template( 'single-room-type/tabs/amenities.php' );
	}
}

if ( ! function_exists( 'abkng_room_type_extra_services_tab' ) ) {

	/**
	 * Output the attributes tab content.
	 *
	 * @subpackage	Room type/Tabs
	 */
	function abkng_room_type_extra_services_tab() {
		abkng_get_template( 'single-room-type/tabs/extra_services.php' );
	}
}

if ( ! function_exists( 'abkng_default_room_type_tabs' ) ) {

	/**
	 * Add default room type tabs to room type pages.
	 *
	 * @param array $tabs tabs.
	 * @return array
	 */
	function abkng_default_room_type_tabs( $tabs = array() ) {
		global $room_type, $post;

		// Description tab - shows room type content.
		if ( $post->post_content ) {
			$tabs['description'] = array(
				'title'    => __( 'Description', 'awebooking' ),
				'priority' => 10,
				'callback' => 'abkng_room_type_description_tab',
			);
		}

		// Optional amenities tab - shows attributes.
		if ( $room_type && $room_type['amenity_ids'] ) {
			$tabs['amenities'] = array(
				'title'    => __( 'Amenities', 'awebooking' ),
				'priority' => 20,
				'callback' => 'abkng_room_type_amenities_tab',
			);
		}

		// Optional extra services - shows attributes.
		if ( $room_type && $room_type['service_ids'] ) {
			$tabs['extra_services'] = array(
				'title'    => __( 'Extra Services', 'awebooking' ),
				'priority' => 30,
				'callback' => 'abkng_room_type_extra_services_tab',
			);
		}

		return $tabs;
	}
}

if ( ! function_exists( 'abkng_sort_room_type_tabs' ) ) {

	/**
	 * Sort tabs by priority.
	 *
	 * @param array $tabs tabs.
	 * @return array
	 */
	function abkng_sort_room_type_tabs( $tabs = array() ) {

		// Make sure the $tabs parameter is an array.
		if ( ! is_array( $tabs ) ) {
			trigger_error( 'Function abkng_sort_room_type_tabs() expects an array as the first parameter. Defaulting to empty array.' );
			$tabs = array();
		}

		// Re-order tabs by priority.
		if ( ! function_exists( 'abkng_sort_priority_callback' ) ) {

			/**
			 * Sort priority callback.
			 *
			 * @param  array $a array.
			 * @param  array $b array.
			 * @return array
			 */
			function abkng_sort_priority_callback( $a, $b ) {
				if ( $a['priority'] === $b['priority'] ) {
					return 0;
				}

				return ( $a['priority'] < $b['priority'] ) ? -1 : 1;
			}
		}

		uasort( $tabs, 'abkng_sort_priority_callback' );

		return $tabs;
	}
}

if ( ! function_exists( 'abkng_pagination' ) ) {
	function abkng_pagination() {
		abkng_get_template( 'loop/pagination.php' );
	}
}

/**
 * Outputs a list of room type attributes for a room type.
 *
 * @param  Room_Type $room_type room type obj.
 */
function abkng_display_room_type_attributes( $room_type ) {
	abkng_get_template( 'single-room-type/room-type-attributes.php', array(
		'room_type'            => $room_type,
	) );
}

/**
 * Outputs check availability form.
 */
function abkng_template_check_availability_form() {
	abkng_get_template( 'check-availability-form.php' );
}

/**
 * Outputs check availability form for input time.
 */
function abkng_template_check_form_input_time() {
	$date_format  = abkng_config( 'date_format' );
	$date_format = Formatting::php_to_js_dateformat( $date_format );

	abkng_get_template( 'check-form/input-time.php', array(
		'date_format'   => $date_format,
	) );
}

/**
 * Outputs check availability form for input location.
 */
function abkng_template_check_form_input_location() {

	$locations = get_terms( 'hotel_location', array(
		'hide_empty' => true,
	) );

	$term_default = Utils::get_hotel_location_default();

	abkng_get_template( 'check-form/input-location.php', array(
		'locations'     => $locations,
		'term_default'  => $term_default,
	) );
}

/**
 * Outputs check availability form for input capacity.
 */
function abkng_template_check_form_input_capacity() {
	$max_adults   = abkng_config( 'check_availability_max_adults' );
	$max_children = abkng_config( 'check_availability_max_children' );

	abkng_get_template( 'check-form/input-capacity.php', array(
		'max_adults' 	=> $max_adults,
		'max_children' 	=> $max_children,
	) );
}

if ( ! function_exists( 'abkng_template_notices' ) ) :
	/**
	 * AweBooking notices template.
	 *
	 * @return void
	 */
	function abkng_template_notices() {
		abkng_get_template( 'global/notices.php' );
	}
endif;

if ( ! function_exists( 'abkng_template_checkout_customer_form' ) ) :
	/**
	 * AweBooking checkout customer form template.
	 *
	 * @return void
	 */
	function abkng_template_checkout_customer_form() {
		abkng_get_template( 'checkout/customer-form.php' );
	}
endif;

if ( ! function_exists( 'abkng_template_checkout_extra_service_details' ) ) :
	/**
	 * AweBooking checkout extra service details template.
	 *
	 * @return void
	 */
	function abkng_template_checkout_general_informations( $availability, $room_type ) {
		$extra_services = $availability->get_request()->get_request( 'extra_services' );
		$extra_services_name = [];

		foreach ( $extra_services as $key => $id ) {
			$term = get_term( $id, AweBooking::HOTEL_SERVICE );
			$extra_services_name[] = $term->name;
		}

		abkng_get_template( 'checkout/general-informations.php', array(
			'extra_services_name' => $extra_services_name,
			'availability'        => $availability,
			'room_type'           => $room_type,
		));
	}
endif;
