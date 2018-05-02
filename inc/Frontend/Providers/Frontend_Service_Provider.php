<?php
namespace AweBooking\Frontend\Providers;

use AweBooking\Support\Service_Provider;
use AweBooking\Component\Routing\Namespace_Route;

class Frontend_Service_Provider extends Service_Provider {
	/**
	 * Registers services on the plugin.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'after_setup_theme', [ $this, 'include_functions' ], 11 );
	}

	/**
	 * Init (boot) the service provider.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'awebooking/register_routes', [ $this, 'register_routes' ], 1 );
	}

	/**
	 * Function used to init AwweBooking template functions.
	 *
	 * This makes them pluggable by plugins and themes.
	 *
	 * @access private
	 */
	public function include_functions() {
		include_once dirname( __DIR__ ) . '/functions.php';
	}

	/**
	 * Register admin routes.
	 *
	 * @param \FastRoute\RouteCollector $route The route collector.
	 * @access private
	 */
	public function register_routes( $route ) {
		$route = new Namespace_Route( $route, 'AweBooking\\Frontend\\Controllers' );

		require dirname( __DIR__ ) . '/routes.php';
	}
}
