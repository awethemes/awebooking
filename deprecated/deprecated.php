<?php

use AweBooking\Deprecated\Setting;

function _abrs_310_deprecated_function( $function ) {
	_deprecated_function( $function, '3.1.0' );
}

function awebooking_option( $key, $default = null ) {
	return abrs_get_option( $key, $default );
}

function _abrs_310_load_deprecated( $plugin ) {
	$plugin->singleton( 'config', function() {
		return new Setting;
	});

	$plugin->alias( 'config', 'setting' );

	class_alias( 'AweBooking\Deprecated\Support\Formatting', 'AweBooking\Support\Formatting' );
	class_alias( 'AweBooking\Deprecated\Model\Amenity', 'AweBooking\Hotel\Amenity' );

	class_alias( 'AweBooking\Model\Model', 'AweBooking\Model\WP_Object' );
	class_alias( 'AweBooking\Model\Model', 'AweBooking\Support\WP_Object' );

	class_alias( 'AweBooking\Model\Room', 'AweBooking\Hotel\Room' );
	class_alias( 'AweBooking\Model\Room_Type', 'AweBooking\Hotel\Room_Type' );
	class_alias( 'AweBooking\Model\Service', 'AweBooking\Hotel\Service' );
	class_alias( 'AweBooking\Model\Booking', 'AweBooking\Booking\Booking' );

	class_alias( 'AweBooking\Checkout\Checkout', 'AweBooking\Frontend\Checkout\Checkout' );
}
add_action( 'awebooking_booting', '_abrs_310_load_deprecated' );
