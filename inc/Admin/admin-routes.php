<?php

use AweBooking\Admin\Controllers\Tax_Controller;
use AweBooking\Admin\Controllers\Rate_Controller;
use AweBooking\Admin\Controllers\About_Controller;
use AweBooking\Admin\Controllers\Source_Controller;
use AweBooking\Admin\Controllers\Calendar_Controller;
use AweBooking\Admin\Controllers\Settings_Controller;
use AweBooking\Admin\Controllers\Reservation_Controller;
use AweBooking\Admin\Controllers\Booking_Room_Controller;
use AweBooking\Admin\Controllers\Booking_Payment_Controller;

$route->get( '/about', About_Controller::class . '@about' );
$route->post( '/settings', Settings_Controller::class . '@store' );

$route->get( '/rates', Rate_Controller::class . '@index' );
$route->post( '/rates', Rate_Controller::class . '@update' );

$route->get( '/calendar', Calendar_Controller::class . '@index' );
$route->post( '/calendar', Calendar_Controller::class . '@update' );

$route->get( '/reservation', Reservation_Controller::class . '@index' );
$route->post( '/reservation', Reservation_Controller::class . '@update' );

// Tax routes.
$route->get( '/tax/create', Tax_Controller::class . '@create' );
$route->post( '/tax/store', Tax_Controller::class . '@store' );
$route->get( '/tax/{tax:\d+}', Tax_Controller::class . '@show' );
$route->put( '/tax/{tax:\d+}', Tax_Controller::class . '@update' );
$route->delete( '/tax/{tax:\d+}/delete', Tax_Controller::class . '@delete' );

// Source routes.
$route->post( '/sources', Source_Controller::class . '@store' );
$route->post( '/sources/bulk-update', Source_Controller::class . '@bulk_update' );
$route->get( '/source/{source}', Source_Controller::class . '@show' );
$route->put( '/source/{source}', Source_Controller::class . '@update' );

// Booking routes.
$route->addGroup( '/booking/{booking:\d+}', function ( $r ) {
	$r->post( '/add_note', Booking_Controller::class . '@add_note' );
	$r->post( '/delete_note', Booking_Controller::class . '@delete_note' );

	$r->get( '/payment/create', Booking_Payment_Controller::class . '@create' );
	$r->post( '/payment', Booking_Payment_Controller::class . '@store' );
	$r->get( '/payment/{payment_item:\d+}/edit', Booking_Payment_Controller::class . '@edit' );
	$r->put( '/payment/{payment_item:\d+}', Booking_Payment_Controller::class . '@update' );
	$r->delete( '/payment/{payment_item:\d+}', Booking_Payment_Controller::class . '@destroy' );

	$r->get( '/room/add', Booking_Room_Controller::class . '@create' );
	$r->post( '/room', Booking_Room_Controller::class . '@store' );
	$r->get( '/room/{room_item:\d+}/edit', Booking_Room_Controller::class . '@edit' );
	$r->get( '/room/{room_item:\d+}/swap', Booking_Room_Controller::class . '@edit' );
	$r->put( '/room/{room_item:\d+}', Booking_Room_Controller::class . '@update' );
	$r->delete( '/room/{room_item:\d+}', Booking_Room_Controller::class . '@destroy' );
});
