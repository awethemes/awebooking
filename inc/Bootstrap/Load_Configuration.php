<?php
namespace AweBooking\Bootstrap;

use AweBooking\Setting;
use AweBooking\AweBooking;
use AweBooking\Multilingual;
use AweBooking\Support\Decimal;
use AweBooking\Support\Carbonate;
use AweBooking\Money\Price;

class Load_Configuration {
	/**
	 * The Multilingual instance.
	 *
	 * @var \AweBooking\Multilingual
	 */
	protected $multilingual;

	/**
	 * Load the configuration.
	 *
	 * @param Multilingual $multilingual The Multilingual instance.
	 */
	public function __construct( Multilingual $multilingual ) {
		$this->multilingual = $multilingual;
	}

	/**
	 * Bootstrap the AweBooking.
	 *
	 * @param  AweBooking $awebooking The AweBooking instance.
	 * @return void
	 */
	public function bootstrap( AweBooking $awebooking ) {
		$awebooking->instance( 'setting_key', $this->get_setting_key( $awebooking ) );

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

	/**
	 * Returns the setting key.
	 *
	 * "awebooking_settings" or "awebooking_settings_en", ...
	 *
	 * @param  AweBooking $awebooking The AweBooking instance.
	 * @return string
	 */
	protected function get_setting_key( AweBooking $awebooking ) {
		$setting_key = 'awebooking_settings';

		if ( $awebooking->is_running_multilanguage() ) {
			$active_language = $this->multilingual->get_active_language();

			// If active language is not "en", "" or all, suffix with current language.
			if ( ! in_array( $active_language, [ '', 'en', 'all' ] ) ) {
				$setting_key .= '_' . $active_language;
			}
		}

		return $setting_key;
	}
}
