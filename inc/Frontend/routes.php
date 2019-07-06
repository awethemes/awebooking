<?php

use AweBooking\Component\Routing\Namespace_Route;

$route = new Namespace_Route( $route, 'AweBooking\\Frontend\\Controllers' );

$route->post( '/checkout', 'Checkout_Controller@checkout' );

$route->get( '/payment/{hash}', 'Payment_Controller@show' );
$route->post( '/payment/{hash}', 'Payment_Controller@process' );

$route->post( '/reservation/services', 'Reservation_Controller@services' );
$route->post( '/reservation/book-room', 'Reservation_Controller@book' );
$route->get( '/reservation/remove/{row_id}', 'Reservation_Controller@remove' );
