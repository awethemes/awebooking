<?php
namespace AweBooking\Admin\Providers;

use AweBooking\Admin\Admin_Settings;
use AweBooking\Admin\Admin_Template;
use AweBooking\Component\Routing\Namespace_Route;
use AweBooking\Support\Service_Provider;

class Admin_Service_Provider extends Service_Provider {
	/**
	 * Registers services on the plugin.
	 *
	 * @access private
	 */
	public function register() {
		$this->plugin->singleton( 'admin_template', function() {
			return new Admin_Template;
		});

		$this->plugin->singleton( 'admin_settings', function() {
			return new Admin_Settings( $this->plugin );
		});

		$this->plugin->alias( 'admin_template', Admin_Template::class );
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

		// Register the admin routes.
		add_action( 'awebooking/register_admin_routes', [ $this, 'register_admin_routes' ], 1 );
		add_action( 'admin_init', [ $this, 'register_admin_settings' ] );

		// Trim price zeros in admin area.
		add_filter( 'awebooking/price_trim_zeros', '__return_true' );
	}

	/**
	 * Register admin routes.
	 *
	 * @param \FastRoute\RouteCollector $route The route collector.
	 * @access private
	 */
	public function register_admin_routes( $route ) {
		$route = new Namespace_Route( $route, 'AweBooking\\Admin\\Controllers' );

		require dirname( __DIR__ ) . '/admin-routes.php';
	}

	/**
	 * Register admin settings.
	 *
	 * @access private
	 */
	public function register_admin_settings() {
		$this->plugin->make( 'admin_settings' )->setup();
	}
}
