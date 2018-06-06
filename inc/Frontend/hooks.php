<?php

// @codingStandardsIgnoreStart
add_filter( 'body_class',                            'abrs_body_class'                       );

add_action( 'awebooking_print_notices',              'abrs_print_notices'                    );
add_action( 'awebooking_display_search_result_item', 'awebooking_search_result_item', 10, 4  );

add_action( 'awebooking_checkout_guest_details',     'awebooking_checkout_guest_details', 10 );
add_action( 'awebooking_checkout_guest_details',     'awebooking_checkout_additionals', 20   );
add_action( 'awebooking_checkout_payments',          'awebooking_checkout_payments', 10      );

add_action( 'awebooking/before_main_content', 'awebooking_output_content_wrapper', 10 );
add_action( 'awebooking/after_main_content', 'awebooking_output_content_wrapper_end', 10 );

add_action( 'awebooking/template_notices', 'awebooking_template_notices', 10 );

add_action( 'awebooking/sidebar', 'awebooking_get_sidebar', 10 );

add_action( 'awebooking/before_archive_loop_item', 'awebooking_template_loop_room_type_link_open', 10 );
add_action( 'awebooking/before_archive_loop_item_title', 'awebooking_template_loop_room_type_link_close', 20 );

add_action( 'awebooking/after_archive_loop_item', 'awebooking_template_loop_view_more', 10 );
add_action( 'awebooking/before_archive_loop_item_title', 'awebooking_template_loop_room_type_thumbnail', 10 );
add_action( 'awebooking/archive_loop_item_title', 'awebooking_template_loop_room_type_title', 10 );

add_action( 'awebooking/after_archive_loop_item_title', 'awebooking_template_loop_price', 10 );
add_action( 'awebooking/after_archive_loop_item_title', 'awebooking_template_loop_description', 20 );


add_action( 'awebooking/before_single_room_type_summary', 'awebooking_show_room_type_images', 20 );
add_action( 'awebooking/room_type_thumbnails', 'awebooking_show_room_type_thumbnails', 10 );

// add_action( 'awebooking/single_room_type_summary', 'awebooking_template_single_title', 5 );
// add_action( 'awebooking/single_room_type_summary', 'awebooking_template_single_price', 10 );
// add_action( 'awebooking/single_room_type_summary', 'awebooking_template_single_form', 15 );

// @codingStandardsIgnoreEnd
