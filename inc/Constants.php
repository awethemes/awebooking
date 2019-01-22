<?php

namespace AweBooking;

class Constants {
	// The core constants.
	const OPTION_KEY        = 'awebooking_settings';
	const BOOKING           = 'awebooking';
	const BOOKING_NOTE      = 'booking_note';
	const ROOM_TYPE         = 'room_type';
	const HOTEL_RATE        = 'hotel_rate';
	const HOTEL_RATE_PLAN   = 'hotel_rate_plan';
	const HOTEL_LOCATION    = 'hotel_location';
	const HOTEL_AMENITY     = 'hotel_amenity';
	const HOTEL_SERVICE     = 'hotel_service';
	const HOTEL_SERVICE_CAT = 'hotel_service_cat';
	const PARENT_MENU_SLUG  = 'awebooking';

	// Booking constants.
	const STATE_AVAILABLE   = 0;
	const STATE_UNAVAILABLE = 1;
	const STATE_BOOKING     = 2;
	const STATE_SYNC        = 3;

	// Granularity levels.
	const GL_DAILY   = 'daily';
	const GL_NIGHTLY = 'nightly';

	// Reservation modes.
	const MODE_SINGLE   = 'single_room';
	const MODE_MULTIPLE = 'multiple_room';

	/**
	 * Defines constants.
	 *
	 * @param \AweBooking\Plugin $plugin
	 * @return void
	 */
	public static function defines( $plugin ) {
		static::define( 'ABRS_ABSPATH', $plugin->plugin_path() );
		static::define( 'ABRS_ASSET_URL', $plugin->plugin_url( 'assets/' ) );
		static::define( 'ABRS_ROUNDING_PRECISION', 6 );
		static::define( 'ABRS_TEMPLATE_DEBUG', false );
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param string      $name  Constant name.
	 * @param string|bool $value Constant value.
	 */
	public static function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}
}
