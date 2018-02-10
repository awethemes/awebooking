<?php
use AweBooking\Http\Controllers\Reservation_Controller;

$route->post( '/reservation', Reservation_Controller::class . '@create' );
