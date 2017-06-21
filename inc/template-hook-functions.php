<?php
/**
 * Awebooking Template Hooks
 *
 * Action/filter hooks used for Awebooking functions/templates.
 *
 * @author 		Awethemes
 * @category 	Core
 * @package 	Awethemes/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Content Wrappers.
 *
 * @see abkng_output_content_wrapper()
 * @see abkng_output_content_wrapper_end()
 */
add_action( 'awebooking/before_main_content', 'abkng_output_content_wrapper', 10 );
add_action( 'awebooking/after_main_content', 'abkng_output_content_wrapper_end', 10 );

add_action( 'awebooking/template_notices', 'abkng_template_notices', 10 );

/**
 * Room types Loop.
 *
 * @see abkng_location_filter()
 * @see abkng_catalog_ordering()
 */
add_action( 'awebooking/before_archive_loop', 'abkng_location_filter', 10 );
add_action( 'awebooking/before_archive_loop', 'abkng_catalog_ordering', 20 );

/**
 * Sidebar.
 *
 * @see abkng_get_sidebar()
 */
add_action( 'awebooking/sidebar', 'abkng_get_sidebar', 10 );

/**
 * Room Type Loop Items.
 *
 * @see abkng_template_loop_room_type_link_open()
 * @see abkng_template_loop_room_type_link_close()
 * @see abkng_template_loop_view_more()
 * @see abkng_template_loop_room_type_thumbnail()
 * @see abkng_template_loop_room_type_title()
 * @see abkng_template_loop_price()
 */
add_action( 'awebooking/before_archive_loop_item', 'abkng_template_loop_room_type_link_open', 10 );
add_action( 'awebooking/before_archive_loop_item_title', 'abkng_template_loop_room_type_link_close', 20 );

add_action( 'awebooking/after_archive_loop_item', 'abkng_template_loop_view_more', 10 );
add_action( 'awebooking/before_archive_loop_item_title', 'abkng_template_loop_room_type_thumbnail', 10 );
add_action( 'awebooking/archive_loop_item_title', 'abkng_template_loop_room_type_title', 10 );

add_action( 'awebooking/after_archive_loop_item_title', 'abkng_template_loop_price', 10 );
add_action( 'awebooking/after_archive_loop_item_title', 'abkng_template_loop_description', 20 );

/**
 * Before Single Room type Summary Div.
 *
 * @see abkng_show_room_type_images()
 */
add_action( 'awebooking/before_single_room_type_summary', 'abkng_show_room_type_images', 20 );
add_action( 'awebooking/room_type_thumbnails', 'abkng_show_room_type_thumbnails', 10 );

/**
 * Single Room type Summary Div.
 *
 * @see abkng_template_single_title()
 * @see abkng_template_single_price()
 * @see abkng_template_single_form()
 */
add_action( 'awebooking/single_room_type_summary', 'abkng_template_single_title', 5 );
add_action( 'awebooking/single_room_type_summary', 'abkng_template_single_price', 10 );
add_action( 'awebooking/single_room_type_summary', 'abkng_template_single_form', 15 );

/**
 * After Single Room type Summary Div.
 *
 * @see abkng_output_room_type_data_tabs()
 */
add_action( 'awebooking/after_single_room_type_summary', 'abkng_output_room_type_data_tabs', 10 );

/**
 * Room type page tabs.
 */
add_filter( 'awebooking/room_type_tabs', 'abkng_default_room_type_tabs' );
add_filter( 'awebooking/room_type_tabs', 'abkng_sort_room_type_tabs', 99 );

/**
 * Optional Extras tab.
 *
 * @see abkng_display_room_type_attributes()
 */
add_action( 'awebooking/room_type_amenities', 'abkng_display_room_type_attributes', 10 );

/**
 * Check availability area.
 *
 * @see abkng_template_check_availability_form()
 */
add_action( 'awebooking/check_availability_area', 'abkng_template_check_availability_form', 10 );

/**
 * Get template pagination.
 *
 * @see abkng_pagination()
 */
add_action( 'awebooking/after_archive_loop', 'abkng_pagination', 10 );

/**
 * Checkout.
 *
 * @see abkng_template_checkout_general_informations()
 * @see abkng_template_checkout_customer_form()
 */
add_action( 'awebooking/checkout/detail_tables', 'abkng_template_checkout_general_informations', 10, 2 );
add_action( 'awebooking/checkout/customer_form', 'abkng_template_checkout_customer_form', 10 );
