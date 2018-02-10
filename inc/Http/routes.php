<?php

use AweBooking\Http\Controllers\Checkout_Controller;
use AweBooking\Http\Controllers\Reservation_Controller;

$route->post( '/reservation', Reservation_Controller::class . '@add_item' );
$route->post( '/reservation/remove_item', Reservation_Controller::class . '@remove_item' );
$route->post( '/checkout/process', Checkout_Controller::class . '@process' );
