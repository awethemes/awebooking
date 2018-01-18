<?php

use AweBooking\Admin\Controllers\Settings_Controller;
use AweBooking\Admin\Controllers\Reservation_Controller;
use AweBooking\Admin\Controllers\Source_Controller;

$route->post( '/settings', Settings_Controller::class . '@store' );

// Reservation source routes.
$route->post( '/sources', Source_Controller::class . '@store' );
$route->post( '/sources/{source}', Source_Controller::class . '@update' );

// Reservation routes.
$route->get( '/reservation/create', Reservation_Controller::class . '@create' );
$route->post( '/reservation/add_item', Reservation_Controller::class . '@add_item' );
