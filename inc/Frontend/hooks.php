<?php

// @codingStandardsIgnoreStart
add_filter( 'body_class',                  'abrs_body_class' );

// Checkout
add_action( 'awebooking/checkout_guest_details', 'awebooking_checkout_guest_details', 10 );
add_action( 'awebooking/checkout_guest_details', 'awebooking_checkout_additionals', 20 );
add_action( 'awebooking/checkout_payments',      'awebooking_checkout_payments', 10 );

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
// add_action( 'awebooking/single_room_type_summary', 'awebooking_template_single_title', 5 );
// add_action( 'awebooking/single_room_type_summary', 'awebooking_template_single_price', 10 );
// add_action( 'awebooking/single_room_type_summary', 'awebooking_template_single_form', 15 );

// @codingStandardsIgnoreEnd
