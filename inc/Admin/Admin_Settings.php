<?php
namespace AweBooking\Admin;

use AweBooking\Plugin;
use AweBooking\Support\Collection;
use AweBooking\Admin\Settings\Setting;
use Awethemes\Http\Request;

class Admin_Settings {
	/**
	 * The plugin instance.
	 *
	 * @var \AweBooking\Plugin
	 */
	protected $plugin;

	/**
	 * All registerd settings.
	 *
	 * @var \AweBooking\Support\Collection
	 */
	protected $settings;

	/**
	 * The core settings.
	 *
	 * @var array
	 */
	protected $core_settings = [
		\AweBooking\Admin\Settings\General_Setting::class,
		\AweBooking\Admin\Settings\Hotel_Setting::class,
		\AweBooking\Admin\Settings\Taxes_Setting::class,
		\AweBooking\Admin\Settings\Checkout_Setting::class,
		\AweBooking\Admin\Settings\Appearance_Setting::class,
		\AweBooking\Admin\Settings\Email_Setting::class,
		\AweBooking\Admin\Settings\Premium_Setting::class,
	];

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Plugin $plugin The plugin instance.
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
		$this->settings = new Collection;
	}

	/**
	 * Get all registerd settings.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function all() {
		return $this->settings;
	}

	/**
	 * Get a registered setting.
	 *
	 * @param  string $setting The setting ID.
	 * @return \AweBooking\Admin\Settings\Setting|null
	 */
	public function get( $setting ) {
		return $this->settings->get( $setting );
	}

	/**
	 * Determines if a given setting ID is registered.
	 *
	 * @param  string $setting The setting ID.
	 * @return bool
	 */
	public function registered( $setting ) {
		return $this->settings->has(
			$setting instanceof Setting ? $setting->get_id() : $setting
		);
	}

	/**
	 * Register a setting.
	 *
	 * @param  \AweBooking\Admin\Settings\Setting $setting The setting instance.
	 * @param  boolean                            $force   Force to register.
	 * @return \AweBooking\Admin\Settings\Setting|false
	 */
	public function register( Setting $setting, $force = false ) {
		if ( ! $setting->get_id() ) {
			return false;
		}

		if ( $this->registered( $setting ) && ! $force ) {
			return $setting;
		}

		return $this->settings[ $setting->get_id() ] = $setting;
	}

	/**
	 * Unregister a registered setting.
	 *
	 * @param  string $setting The setting ID.
	 * @return void
	 */
	public function unregister( $setting ) {
		unset( $this->settings[ $setting ] );
	}

	/**
	 * Setup the settings.
	 *
	 * @access private
	 */
	public function setup() {
		// Clear the settings before.
		$this->settings->clear();

		$settings = apply_filters( 'abrs_admin_settings', $this->core_settings );

		foreach ( $settings as $setting ) {
			$this->register( $this->plugin->make( $setting ) );
		}

		do_action( 'abrs_register_admin_settings', $this );
	}

	/**
	 * Perform handle save a setting.
	 *
	 * @param  string                  $setting The setting name.
	 * @param  \Awethemes\Http\Request $request The http request instance.
	 * @return void
	 */
	public function save( $setting, Request $request ) {
		// Leave if given an empty setting name.
		if ( ! is_string( $setting ) || empty( $setting ) ) {
			return;
		}

		// Makes sure that request was referred from admin page.
		check_admin_referer( 'awebooking-settings' );

		// Handle save the setting.
		if ( apply_filters( 'abrs_handle_save_setting_' . $setting, true ) && $instance = $this->get( $setting ) ) {
			abrs_rescue( function () use ( $instance, $request ) {
				$instance->save( $request );
			});
		}

		// Fire update_setting actions.
		do_action( 'abrs_update_setting_' . $setting, $this );
		do_action( 'abrs_update_settings', $setting, $this );

		// Add an success notices.
		abrs_admin_notices( esc_html__( 'Your settings have been saved.', 'awebooking' ), 'success' )->dialog();

		// Force flush_rewrite_rules.
		@flush_rewrite_rules();

		// Fire abrs_settings_updated action.
		do_action( 'abrs_settings_updated', $this );
	}
}
