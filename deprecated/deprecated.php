<?php

use AweBooking\Plugin;
use AweBooking\Deprecated\Admin\Admin_Menu;

$awebooking = Plugin::get_instance();

$awebooking->singleton( 'admin_menu', function () {
	return new Admin_Menu;
});

require_once trailingslashit( __DIR__ ) . 'back-compat.php';

class_alias( 'AweBooking\Plugin', 'AweBooking\AweBooking' );
class_alias( 'AweBooking\Deprecated\Support\Addon', 'AweBooking\Support\Addon' );

