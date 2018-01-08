<?php

use AweBooking\Admin\Controllers\Settings_Controller;

$route->post( '/settings', Settings_Controller::class . '@store' );
