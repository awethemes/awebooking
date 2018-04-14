<?php
namespace AweBooking\Bootstrap;

use AweBooking\Plugin;
use AweBooking\Multilingual;
use AweBooking\Support\Decimal;
use AweBooking\Support\Carbonate;

class Load_Configuration {
	/**
	 * The plugin instance.
	 *
	 * @var \AweBooking\Plugin
	 */
	protected $plugin;

	/**
	 * Setup environment bootstrapper.
	 *
	 * @param \AweBooking\Plugin $plugin The plugin instance.
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Bootstrap the plugin.
	 *
	 * @return void
	 */
	public function bootstrap() {
		add_action( 'setup_theme', [ $this, 'load_configuration' ] );
	}

	/**
	 * Load the configuration.
	 *
	 * @access private
	 */
	public function load_configuration() {
		$this->plugin->make( Multilingual::class );

		// Set the option key name.
		$this->plugin->set_option_key(
			apply_filters( 'awebooking/option_key_name', 'awebooking_settings' )
		);

		// Maybe set the option name on multi-language.
		$this->maybe_modify_options();

		// Decimal default scale.
		Decimal::set_default_scale( absint( $this->plugin->get_option( 'price_number_decimals', 2 ) ) );

		// Correct the datetime starts and ends of week.
		Carbonate::setWeekStartsAt( (int) get_option( 'start_of_week' ) );
		Carbonate::setWeekEndsAt( (int) calendar_week_mod( Carbonate::getWeekStartsAt() - 1 ) );
	}

	/**
	 * Maybe set the option name on multi-language.
	 *
	 * @return string
	 */
	protected function maybe_modify_options() {
		if ( ! abrs_running_on_multilanguage() ) {
			return;
		}

		$current_key = $this->plugin->get_option_key();
		$current_language = $this->plugin['multilingual']->get_current_language();

		// Prevent modify option key if current language is: "en", "" or "all".
		if ( empty( $current_language ) || in_array( $current_language, [ '', 'en', 'all' ] ) ) {
			return;
		}

		// Set new option key, suffix with current language.
		$this->plugin->set_option_key(
			$new_key = $current_key . '_' . $current_language
		);

		// Perform copy options only in admin.
		if ( abrs_request_is( 'admin' ) ) {
			$this->perform_copy_options( $current_key, $new_key );
		}
	}

	/**
	 * Perform the copy options.
	 *
	 * @param  string $current_key The current option key.
	 * @param  string $new_key     The new option key.
	 * @return void
	 */
	protected function perform_copy_options( $current_key, $new_key ) {
		$new_options     = (array) get_option( $new_key, [] );
		$current_options = (array) get_option( $current_key, [] );

		if ( empty( $new_options ) && $current_options ) {
			update_option( $new_key, $current_options );
		}
	}
}
