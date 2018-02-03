<?php

use AweBooking\Admin\Controllers\Settings_Controller;
use AweBooking\Admin\Controllers\Reservation_Controller;
use AweBooking\Admin\Controllers\Booking_Payment_Controller;
use AweBooking\Admin\Controllers\Source_Controller;
use AweBooking\Admin\Controllers\Tax_Controller;

$route->post( '/settings', Settings_Controller::class . '@store' );

// Reservation source routes.
$route->post( '/sources', Source_Controller::class . '@store' );
$route->post( '/sources/bulk-update', Source_Controller::class . '@bulk_update' );
$route->post( '/sources/{source}', Source_Controller::class . '@update' );

// Reservation routes.
$route->get( '/reservation/create', Reservation_Controller::class . '@create' );
$route->post( '/reservation/add_item', Reservation_Controller::class . '@add_item' );

// Booking routes.
$route->get( '/booking/{booking:\d+}/payment/create', Booking_Payment_Controller::class . '@create' );
$route->post( '/booking/{booking:\d+}/payment', Booking_Payment_Controller::class . '@store' );
$route->get( '/booking/{booking:\d+}/payment/{payment_item:\d+}/edit', Booking_Payment_Controller::class . '@edit' );
$route->put( '/booking/{booking:\d+}/payment/{payment_item:\d+}', Booking_Payment_Controller::class . '@update' );
$route->delete( '/booking/{booking:\d+}/payment/{payment_item:\d+}', Booking_Payment_Controller::class . '@destroy' );

// Reservation tax routes.
$route->get( '/tax/create', Tax_Controller::class . '@create' );
$route->post( '/tax/store', Tax_Controller::class . '@store' );

$route->get( '/tax/{tax:\d+}', Tax_Controller::class . '@show' );
$route->put( '/tax/{tax:\d+}', Tax_Controller::class . '@update' );

$route->get( '/tax/{tax:\d+}/delete', Tax_Controller::class . '@delete' );
