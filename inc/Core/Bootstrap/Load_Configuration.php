<?php

namespace AweBooking\Core\Bootstrap;

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
		add_action( 'after_setup_theme', [ $this, 'load_configuration' ], 5 );
	}

	/**
	 * Load the configuration.
	 *
	 * @access private
	 */
	public function load_configuration() {
		$this->plugin->make( Multilingual::class );

		// Retrieve the plugin options.
		$this->plugin->retrieve_options();

		// Maybe set the option name on multi-language.
		$this->maybe_modify_options();

		// Correct the datetime starts and ends of week.
		Carbonate::setWeekStartsAt( (int) get_option( 'start_of_week' ) );
		Carbonate::setWeekEndsAt( (int) calendar_week_mod( Carbonate::getWeekStartsAt() - 1 ) );

		Decimal::set_default_scale( absint( $this->plugin->get_option( 'price_number_decimals', 2 ) ) );
	}

	/**
	 * Maybe set the option name on multi-language.
	 *
	 * @return void
	 */
	protected function maybe_modify_options() {
		if ( ! abrs_running_on_multilanguage() ) {
			return;
		}

		$new_option = abrs_normalize_option_name( abrs_multilingual()->get_current_language() );
		$this->plugin->set_options( $new_option );

		// Perform copy options only in admin.
		$original_option = $this->plugin->get_original_option();

		if ( $new_option !== $original_option && is_admin() ) {
			$this->perform_copy_options( $original_option, $new_option );
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
