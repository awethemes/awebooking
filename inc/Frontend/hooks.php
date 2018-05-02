<?php

// @codingStandardsIgnoreStart
add_filter( 'body_class',                  'abrs_body_class' );

// Checkout
add_action( 'awebooking/checkout_guest_details', 'awebooking_checkout_guest_details', 10 );
add_action( 'awebooking/checkout_guest_details', 'awebooking_checkout_additionals', 20 );
add_action( 'awebooking/checkout_payments',      'awebooking_checkout_payments', 10 );
// @codingStandardsIgnoreEnd
