<?php

use AweBooking\Component\Routing\Namespace_Route;

$route = new Namespace_Route( $route, 'AweBooking\\Frontend\\Controllers' );

$route->post( '/checkout', 'Checkout_Controller@checkout' );
$route->post( '/book-room', 'Reservation_Controller@book' );
