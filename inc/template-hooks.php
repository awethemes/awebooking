<?php

/**
 * Content Wrappers.
 *
 * @see awebooking_output_content_wrapper()
 * @see awebooking_output_content_wrapper_end()
 */
add_action( 'awebooking/before_main_content', 'awebooking_output_content_wrapper', 10 );
add_action( 'awebooking/after_main_content', 'awebooking_output_content_wrapper_end', 10 );

add_action( 'awebooking/template_notices', 'awebooking_template_notices', 10 );

/**
 * Room types Loop.
 *
 * @see awebooking_location_filter()
 * @see awebooking_catalog_ordering()
 */
add_action( 'awebooking/before_archive_loop', 'awebooking_location_filter', 10 );
add_action( 'awebooking/before_archive_loop', 'awebooking_catalog_ordering', 20 );

/**
 * Sidebar.
 *
 * @see awebooking_get_sidebar()
 */
add_action( 'awebooking/sidebar', 'awebooking_get_sidebar', 10 );

/**
 * Room Type Loop Items.
 *
 * @see awebooking_template_loop_room_type_link_open()
 * @see awebooking_template_loop_room_type_link_close()
 * @see awebooking_template_loop_view_more()
 * @see awebooking_template_loop_room_type_thumbnail()
 * @see awebooking_template_loop_room_type_title()
 * @see awebooking_template_loop_price()
 */
add_action( 'awebooking/before_archive_loop_item', 'awebooking_template_loop_room_type_link_open', 10 );
add_action( 'awebooking/before_archive_loop_item_title', 'awebooking_template_loop_room_type_link_close', 20 );

add_action( 'awebooking/after_archive_loop_item', 'awebooking_template_loop_view_more', 10 );
add_action( 'awebooking/before_archive_loop_item_title', 'awebooking_template_loop_room_type_thumbnail', 10 );
add_action( 'awebooking/archive_loop_item_title', 'awebooking_template_loop_room_type_title', 10 );

add_action( 'awebooking/after_archive_loop_item_title', 'awebooking_template_loop_price', 10 );
add_action( 'awebooking/after_archive_loop_item_title', 'awebooking_template_loop_description', 20 );

/**
 * Before Single Room type Summary Div.
 *
 * @see awebooking_show_room_type_images()
 */
add_action( 'awebooking/before_single_room_type_summary', 'awebooking_show_room_type_images', 20 );
add_action( 'awebooking/room_type_thumbnails', 'awebooking_show_room_type_thumbnails', 10 );

/**
 * Single Room type Summary Div.
 *
 * @see awebooking_template_single_title()
 * @see awebooking_template_single_price()
 * @see awebooking_template_single_form()
 */
add_action( 'awebooking/single_room_type_summary', 'awebooking_template_single_title', 5 );
add_action( 'awebooking/single_room_type_summary', 'awebooking_template_single_price', 10 );
add_action( 'awebooking/single_room_type_summary', 'awebooking_template_single_form', 15 );

/**
 * After Single Room type Summary Div.
 *
 * @see awebooking_output_room_type_data_tabs()
 */
add_action( 'awebooking/after_single_room_type_summary', 'awebooking_output_room_type_data_tabs', 10 );

/**
 * Room type page tabs.
 */
add_filter( 'awebooking/room_type_tabs', 'awebooking_default_room_type_tabs' );

/**
 * Optional Extras tab.
 *
 * @see awebooking_display_room_type_attributes()
 */
add_action( 'awebooking/room_type_amenities', 'awebooking_display_room_type_attributes', 10 );

/**
 * Check availability area.
 *
 * @see awebooking_template_check_availability_form()
 */
add_action( 'awebooking/check_availability_area', 'awebooking_template_check_availability_form', 10 );

/**
 * Get template pagination.
 *
 * @see awebooking_pagination()
 */
add_action( 'awebooking/after_archive_loop', 'awebooking_pagination', 10 );

/**
 * Checkout.
 *
 * @see awebooking_template_checkout_general_informations()
 * @see awebooking_template_checkout_customer_form()
 */
add_action( 'awebooking/checkout/detail_tables', 'awebooking_template_checkout_general_informations', 10 );
add_action( 'awebooking/checkout/customer_form', 'awebooking_template_checkout_customer_form', 10 );
