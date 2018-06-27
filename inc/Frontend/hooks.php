<?php

// @codingStandardsIgnoreStart
add_filter( 'body_class',                       'abrs_body_class'                       );

add_action( 'abrs_print_notices',               'abrs_print_notices'                    );
add_action( 'abrs_before_main_content',         'abrs_content_wrapper_before', 10       );
add_action( 'abrs_after_main_content',          'abrs_content_wrapper_after', 10        );

add_action( 'abrs_before_search_content',       'abrs_search_form_on_search', 10        );
// add_action( 'abrs_before_search_content',       'abrs_search_form_on_search', 10        );
add_action( 'abrs_display_search_result_item',  'abrs_search_result_item', 10, 4        );

add_action( 'abrs_before_checkout_form',        'abrs_checkout_services', 10            );
add_action( 'abrs_html_checkout_guest_details', 'abrs_checkout_guest_details', 10       );
add_action( 'abrs_html_checkout_guest_details', 'abrs_checkout_additionals', 20         );
add_action( 'abrs_html_checkout_payments',      'abrs_checkout_payments', 10            );
// @codingStandardsIgnoreEnd
