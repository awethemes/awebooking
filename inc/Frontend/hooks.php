<?php

// @codingStandardsIgnoreStart
add_filter( 'body_class',                            'abrs_body_class'                       );

add_action( 'awebooking_print_notices',              'awebooking_print_notices'              );
add_action( 'awebooking_display_search_result_item', 'awebooking_search_result_item', 10, 4  );

add_action( 'awebooking_checkout_guest_details',     'awebooking_checkout_guest_details', 10 );
add_action( 'awebooking_checkout_guest_details',     'awebooking_checkout_additionals', 20   );
add_action( 'awebooking_checkout_payments',          'awebooking_checkout_payments', 10      );
// @codingStandardsIgnoreEnd
