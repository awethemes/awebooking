<?php

namespace AweBooking\Admin\Providers;

use AweBooking\Admin\Template;
use AweBooking\Admin\Admin_Settings;
use AweBooking\Support\Service_Provider;

class Admin_Service_Provider extends Service_Provider {
	/**
	 * Registers services on the plugin.
	 *
	 * @access private
	 */
	public function register() {
		foreach ( [
			\AweBooking\Admin\Settings\General_Setting::class,
			\AweBooking\Admin\Settings\Hotel_Setting::class,
			\AweBooking\Admin\Settings\Taxes_Setting::class,
			\AweBooking\Admin\Settings\Checkout_Setting::class,
			\AweBooking\Admin\Settings\Appearance_Setting::class,
			// \AweBooking\Admin\Settings\Availability_Setting::class,
			\AweBooking\Admin\Settings\Email_Setting::class,
			\AweBooking\Admin\Settings\Premium_Setting::class,
		] as $_class ) {
			$this->plugin->singleton( $_class );
			$this->plugin->tag( $_class, 'settings' );
		}

		$this->plugin->singleton( 'admin_template', function() {
			return new Template;
		});

		$this->plugin->singleton( 'admin_settings', function() {
			return new Admin_Settings( $this->plugin );
		});

		$this->plugin->alias( 'admin_template', Template::class );
		$this->plugin->alias( 'admin_settings', Admin_Settings::class );
	}

	/**
	 * Init (boot) the service provider.
	 *
	 * @return void
	 */
	public function init() {
		// Require the admin functions.
		require_once dirname( __DIR__ ) . '/admin-functions.php';

		add_action( 'abrs_register_admin_routes', [ $this, 'register_admin_routes' ], 1 );

		add_action( 'admin_init', [ $this, 'register_admin_settings' ], 1 );

		// Trim price zeros in admin area.
		add_filter( 'abrs_price_trim_zeros', '__return_true' );
	}

	/**
	 * Register admin routes.
	 *
	 * @param \FastRoute\RouteCollector $route The route collector.
	 * @access private
	 */
	public function register_admin_routes( $route ) {
		require dirname( __DIR__ ) . '/admin-routes.php';
	}

	/**
	 * Perform register admin settings.
	 *
	 * @access private
	 */
	public function register_admin_settings() {
		/* @var $settings \AweBooking\Admin\Admin_Settings  */
		$settings = $this->plugin->make( Admin_Settings::class );

		foreach ( $this->plugin->tagged( 'settings' ) as $setting ) {
			$settings->register( $setting );
			$this->register_setting_modifiers( $setting );
		}

		do_action( 'abrs_register_admin_settings', $settings );
	}

	/**
	 * Perform register modifiers a setting.
	 *
	 * @param \AweBooking\Admin\Settings\Setting $setting The setting instance.
	 */
	protected function register_setting_modifiers( $setting ) {
		$modifiers = $this->plugin->tagged( "setting.{$setting->get_id()}" );

		foreach ( $modifiers as $modifier ) {
			abrs_optional( $modifier )->register();

			if ( method_exists( $modifier, 'save' ) ) {
				add_action( 'abrs_update_setting_' . $setting->get_id(), [ $modifier, 'save' ], 20, 2 );
			}
		}

		do_action( "abrs_register_{$setting->get_id()}_admin_setting", $setting );
	}
}
