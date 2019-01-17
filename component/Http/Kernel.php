<?php

namespace AweBooking\Component\Http;

use AweBooking\Plugin;
use AweBooking\Component\Http\Resolver\Container_Resolver;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Kernel extends Http_Kernel {
	/**
	 * The plugin instance.
	 *
	 * @var \AweBooking\Plugin
	 */
	protected $plugin;

	/**
	 * Create a new HTTP kernel instance.
	 *
	 * @param \AweBooking\Plugin $plugin The plugin instance.
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;

		$this->set_resolver( new Container_Resolver( $this->plugin ) );

		$this->set_logger( $this->plugin->make( 'logger' ) );
	}

	/**
	 * Register the request routes.
	 *
	 * Use FastRoute syntax to register the route,
	 *      $route->get('/get-route', 'get_handler');
	 *      $route->post('/post-route', 'post_handler');
	 *      $route->addRoute('GET', '/do-something', 'function_handler');
	 *
	 * @see https://github.com/nikic/FastRoute#usage
	 *
	 * @param  \FastRoute\RouteCollector $route The route collector.
	 * @return void
	 */
	protected function register_routes( $route ) {
		if ( is_admin() ) {
			do_action( 'abrs_register_admin_routes', $route );
		} else {
			do_action( 'abrs_register_routes', $route );
		}
	}

	/**
	 * {@inheritdoc}
	 */
	protected function handle_found_route( SymfonyRequest $request, array $routeinfo ) {
		// In this, we'll instance the request into the Container.
		$this->plugin->instance( 'request', $request );

		if ( $this->plugin->bound( 'route_binder' ) ) {
			$routeinfo[2] = $this->plugin->make( 'route_binder' )->resolve( $routeinfo[2] );
		}

		return parent::handle_found_route( $request, $routeinfo );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function get_exception_message( $e, $status ) {
		switch ( $status ) {
			case 403:
				return $e->getMessage();
			case 404:
				return esc_html__( 'Sorry, the page you are looking for could not be found.', 'awebooking' );
			default:
				return $e->getMessage() ?: esc_html__( 'Whoops, looks like something went wrong.', 'awebooking' );
		}
	}
}
