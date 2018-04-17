<?php

// @codingStandardsIgnoreStart
$route->get(    '/rates',                              'Rate_Controller@index' );
$route->post(   '/rates',                              'Rate_Controller@update' );
$route->post(   '/rates/bulk-update',                  'Rate_Controller@bulk_update' );

$route->get(    '/calendar',                           'Calendar_Controller@index' );
$route->post(   '/calendar',                           'Calendar_Controller@update' );
$route->post(   '/calendar/bulk-update',               'Calendar_Controller@bulk_update' );

$route->get(    '/settings',                           'Settings_Controller@index' );
$route->post(   '/settings',                           'Settings_Controller@store' );

$route->get(    '/booking-room',                       'Booking_Room_Controller@create' );
$route->post(   '/booking-room',                       'Booking_Room_Controller@store' );
$route->get(    '/booking-room/{room_item:\d+}',       'Booking_Room_Controller@edit' );
$route->put(    '/booking-room/{room_item:\d+}',       'Booking_Room_Controller@update' );
$route->delete( '/booking-room/{room_item:\d+}',       'Booking_Room_Controller@destroy' );

$route->get(    '/booking-payment',                    'Booking_Payment_Controller@create' );
$route->post(   '/booking-payment',                    'Booking_Payment_Controller@store' );
$route->get(    '/booking-payment/{payment_item:\d+}', 'Booking_Payment_Controller@edit' );
$route->put(    '/booking-payment/{payment_item:\d+}', 'Booking_Payment_Controller@update' );
$route->delete( '/booking-payment/{payment_item:\d+}', 'Booking_Payment_Controller@destroy' );

$route->get(    '/about',                              'About_Controller@index' );
$route->get(    '/search/customers',                   'Ajax_Controller@search_customers' );
// @codingStandardsIgnoreEnd
