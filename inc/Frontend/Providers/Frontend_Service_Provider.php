<?php
namespace AweBooking\Frontend\Providers;

use AweBooking\Constants;
use AweBooking\Support\Service_Provider;

class Frontend_Service_Provider extends Service_Provider {
	/**
	 * Init (boot) the service provider.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'abrs_register_routes', [ $this, 'register_routes' ], 1 );

		// Setup the awebooking objects into the main query.
		add_action( 'the_post', [ $this, 'setup_awebooking_objects' ] );
	}

	/**
	 * Register admin routes.
	 *
	 * @param \FastRoute\RouteCollector $route The route collector.
	 * @access private
	 */
	public function register_routes( $route ) {
		require dirname( __DIR__ ) . '/routes.php';
	}

	/**
	 * When `the_post()` is called, setup the awebooking objects.
	 *
	 * @param  \WP_Post $post The WP_Post object (passed by reference).
	 * @return void
	 */
	public function setup_awebooking_objects( $post ) {
		if ( empty( $post->post_type ) ) {
			return;
		}

		if ( Constants::ROOM_TYPE === $post->post_type ) {
			unset( $GLOBALS['room_type'] );
			$GLOBALS['room_type'] = abrs_get_room_type( $post );
		}

		do_action( 'abrs_setup_global_objects', $post );
	}
}
