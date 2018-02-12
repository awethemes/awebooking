<?php

use AweBooking\Admin\Controllers\Settings_Controller;
use AweBooking\Admin\Controllers\Booking_Payment_Controller;

$route->post( '/settings', Settings_Controller::class . '@store' );

// Booking routes.
$route->addGroup( '/booking/{booking:\d+}', function ( $r ) {
	$r->get( '/payment/create', Booking_Payment_Controller::class . '@create' );
	$r->post( '/payment', Booking_Payment_Controller::class . '@store' );
	$r->get( '/payment/{payment_item:\d+}/edit', Booking_Payment_Controller::class . '@edit' );
	$r->put( '/payment/{payment_item:\d+}', Booking_Payment_Controller::class . '@update' );
	$r->delete( '/payment/{payment_item:\d+}', Booking_Payment_Controller::class . '@destroy' );
});
