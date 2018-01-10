<?php
namespace AweBooking\Bootstrap;

use AweBooking\Setting;
use AweBooking\AweBooking;
use AweBooking\Support\Decimal;
use AweBooking\Support\Carbonate;

class Load_Configuration {
	/**
	 * Bootstrap the AweBooking.
	 *
	 * @param  AweBooking $awebooking The AweBooking instance.
	 * @return void
	 */
	public function bootstrap( AweBooking $awebooking ) {
		$awebooking->instance( 'setting_key', 'awebooking_settings' );

		$awebooking->singleton( 'setting', function ( $a ) {
			return new Setting( $a['setting_key'] );
		});

		$awebooking->alias( 'setting', 'config' );
		$awebooking->alias( 'setting', Setting::class );

		// Correct the datetime starts and ends of week.
		Carbonate::setWeekStartsAt( (int) get_option( 'start_of_week' ) );
		Carbonate::setWeekEndsAt( (int) calendar_week_mod( Carbonate::getWeekStartsAt() - 1 ) );

		// Set the default scale for Decimal.
		Decimal::set_default_scale( $awebooking['setting']->get( 'price_number_decimals' ) );
	}
}
