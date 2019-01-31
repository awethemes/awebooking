<?php

use AweBooking\Component\Routing\Namespace_Route;

$route = new Namespace_Route( $route, 'AweBooking\\Admin\\Controllers' );

// @codingStandardsIgnoreStart
$route->get(    '/rates',                              'Rate_Controller@index' );
$route->post(   '/rates',                              'Rate_Controller@update' );
$route->post(   '/rates/bulk-update',                  'Rate_Controller@bulk_update' );
$route->get(    '/calendar',                           'Calendar_Controller@index' );
$route->post(   '/calendar',                           'Calendar_Controller@update' );
$route->post(   '/calendar/bulk-update',               'Calendar_Controller@bulk_update' );
$route->get(    '/export/rates',                       'Export_Controller@rates' );
$route->get(    '/export/calendar',                    'Export_Controller@calendar' );

$route->get(    '/settings',                           'Settings_Controller@index' );
$route->post(   '/settings',                           'Settings_Controller@store' );
$route->get(    '/tools[/{tab}]',                      'Tools_Controller@index' );
$route->post(   '/tools/execute',                      'Tools_Controller@execute' );

$route->get(    '/booking-room',                       'Booking_Room_Controller@search' );
$route->post(   '/booking-room',                       'Booking_Room_Controller@store' );
$route->get(    '/booking-room/{room_item:\d+}',       'Booking_Room_Controller@edit' );
$route->put(    '/booking-room/{room_item:\d+}',       'Booking_Room_Controller@update' );
$route->delete( '/booking-room/{room_item:\d+}',       'Booking_Room_Controller@destroy' );

$route->get(    '/booking-payment',                    'Booking_Payment_Controller@create' );
$route->post(   '/booking-payment',                    'Booking_Payment_Controller@store' );
$route->get(    '/booking-payment/{payment_item:\d+}', 'Booking_Payment_Controller@edit' );
$route->put(    '/booking-payment/{payment_item:\d+}', 'Booking_Payment_Controller@update' );
$route->delete( '/booking-payment/{payment_item:\d+}', 'Booking_Payment_Controller@destroy' );

$route->get(    '/booking-service',                    'Booking_Service_Controller@create' );
$route->post(   '/booking-service',                    'Booking_Service_Controller@store' );
$route->delete( '/booking-service/{service_item:\d+}', 'Booking_Service_Controller@destroy' );

$route->get(    '/about',                              'Misc_Controller@about' );
$route->get(    '/preview-email',                      'Misc_Controller@preview_email' );
$route->get(    '/search/customers',                   'Ajax_Controller@search_customers' );
$route->get(    '/search/services',                    'Ajax_Controller@search_services' );
$route->post(   '/ajax/booking-note',                  'Ajax_Controller@add_booking_note' );
$route->delete( '/ajax/booking-note/{note:\d+}',       'Ajax_Controller@delete_booking_note' );
$route->delete( '/ajax/delete-room/{room:\d+}',        'Ajax_Controller@delete_room' );
$route->get(    '/ajax/rates/check',                   'Ajax_Controller@check_rates' );
// @codingStandardsIgnoreEnd
