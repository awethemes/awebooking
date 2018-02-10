<?php
namespace AweBooking\Providers;

use AweBooking\Setting;
use AweBooking\Cart\Cart;
use AweBooking\Booking\Store;
use AweBooking\Money\Currency;
use AweBooking\Money\Currencies;
use AweBooking\Shortcodes\Shortcodes;
use AweBooking\Support\Service_Provider;

class Core_Service_Provider extends Service_Provider {
	/**
	 * The AweBooking core widgets.
	 *
	 * @var array
	 */
	protected $widgets = [
		\AweBooking\Widgets\Check_Availability_Widget::class,
	];

	/**
	 * Registers services on the AweBooking.
	 */
	public function register() {
		$this->awebooking->singleton( 'currencies', function() {
			return new Currencies;
		});

		$this->awebooking->alias( 'currencies', 'currency_manager' );

		$this->awebooking->singleton( 'currency', function( $a ) {
			return new Currency( $a['setting']->get( 'currency' ) );
		});

		Shortcodes::init();

		add_action( 'widgets_init', function() {
			array_walk( $this->widgets, function( $widget_class ) {
				register_widget( $widget_class );
			});
		});
	}

	/**
	 * Init (boot) the service provider.
	 *
	 * @return void
	 */
	public function init() {
		$this->modify_setting_key();
	}

	/**
	 * Modify the setting key when running on multilanguage.
	 *
	 * @return string
	 */
	protected function modify_setting_key() {
		if ( ! $this->awebooking->is_running_multilanguage() ) {
			return;
		}

		$current_setting = $this->awebooking['setting_key'];
		$active_language = $this->awebooking['multilingual']->get_active_language();

		// If active language is not "en", "" or all, suffix with current language.
		if ( ! in_array( $active_language, [ '', 'en', 'all' ] ) ) {
			$new_setting = $current_setting . '_' . $active_language;

			$this->perform_copy_settings( $current_setting, $new_setting );

			$this->awebooking->instance( 'setting_key', $new_setting );

			if ( $this->awebooking->resolved( 'setting' ) ) {
				$this->awebooking->instance( 'setting', new Setting( $new_setting ) );
			}
		}
	}

	/**
	 * Perform the copy settings.
	 *
	 * @param  string $current_setting The current setting key.
	 * @param  string $new_setting     The new setting key.
	 * @return void
	 */
	protected function perform_copy_settings( $current_setting, $new_setting ) {
		$new_options     = (array) get_option( $new_setting, [] );
		$current_options = (array) get_option( $current_setting, [] );

		if ( empty( $new_options ) && $current_options ) {
			update_option( $new_setting, $current_options );
		}
	}
}
