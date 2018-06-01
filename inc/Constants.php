<?php
namespace AweBooking;

class Constants {
	// The core constants.
	const BOOKING           = 'awebooking';
	const BOOKING_NOTE      = 'booking_note';
	const ROOM_TYPE         = 'room_type';
	const HOTEL_RATE        = 'hotel_rate';
	const HOTEL_RATE_PLAN   = 'hotel_rate_plan';
	const HOTEL_LOCATION    = 'hotel_location';
	const HOTEL_AMENITY     = 'hotel_amenity';
	const HOTEL_SERVICE     = 'hotel_extra_service';

	// Booking constants.
	const STATE_AVAILABLE   = 0;
	const STATE_UNAVAILABLE = 1;
	const STATE_BOOKING     = 2;

	// Granularity levels.
	const GL_DAILY   = 'daily';
	const GL_NIGHTLY = 'nightly';

	/**
	 * Defines constants.
	 *
	 * @return void
	 */
	public static function defines() {
		if ( ! defined( 'ABRS_ABSPATH' ) ) {
			define( 'ABRS_ABSPATH', awebooking()->plugin_path() );
		}

		if ( ! defined( 'ABRS_ASSET_URL' ) ) {
			define( 'ABRS_ASSET_URL', awebooking()->plugin_url( 'assets/' ) );
		}

		if ( ! defined( 'ABRS_TEMPLATE_DEBUG' ) ) {
			define( 'ABRS_TEMPLATE_DEBUG', true );
		}
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
