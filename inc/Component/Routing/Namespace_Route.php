<?php
namespace AweBooking\Component\Routing;

use FastRoute\RouteCollector;

class Namespace_Route {
	/**
	 * The FastRoute collector.
	 *
	 * @var \FastRoute\RouteCollector
	 */
	protected $router;

	/**
	 * The route controller namespace.
	 *
	 * @var string
	 */
	protected $namespace;

	/**
	 * Store the current group prefix.
	 *
	 * @var string
	 */
	protected $group_prefix = '';

	/**
	 * Constructor.
	 *
	 * @param \FastRoute\RouteCollector $router    The FastRoute collector.
	 * @param string                    $namespace The controller namespace.
	 */
	public function __construct( RouteCollector $router, $namespace ) {
		$this->router = $router;
		$this->namespace = $namespace;
	}

	/**
	 * Adds a route.
	 *
	 * @param string|array $method  A string or an array of http method (GET, POST, PUT, PATCH, DELETE).
	 * @param string       $route   The route name.
	 * @param mixed        $handler The route handler.
	 */
	public function route( $method, $route, $handler ) {
		$route = $this->group_prefix . '/' . trim( $route, '/' );

		if ( is_string( $handler ) && 0 !== strpos( $handler, '\\' ) ) {
			$handler = $this->namespace . '\\' . $handler;
		}

		$this->router->addRoute( $method, $route, $handler );
	}

	/**
	 * Create a route group with a common prefix.
	 *
	 * @param string   $prefix   The prefix.
	 * @param callable $callback The callback.
	 */
	public function group( $prefix, callable $callback ) {
		$previous_prefix = $this->group_prefix;

		$this->group_prefix = $previous_prefix . '/' . trim( $prefix, '/' );

		$callback( $this );

		$this->group_prefix = $previous_prefix;
	}

	/**
	 * Adds a GET route.
	 *
	 * @param string $route   The route name.
	 * @param mixed  $handler The route handler.
	 */
	public function get( $route, $handler ) {
		$this->route( 'GET', $route, $handler );
	}

	/**
	 * Adds a POST route.
	 *
	 * @param string $route   The route name.
	 * @param mixed  $handler The route handler.
	 */
	public function post( $route, $handler ) {
		$this->route( 'POST', $route, $handler );
	}

	/**
	 * Adds a PUT route.
	 *
	 * @param string $route   The route name.
	 * @param mixed  $handler The route handler.
	 */
	public function put( $route, $handler ) {
		$this->route( 'PUT', $route, $handler );
	}

	/**
	 * Adds a DELETE route.
	 *
	 * @param string $route   The route name.
	 * @param mixed  $handler The route handler.
	 */
	public function delete( $route, $handler ) {
		$this->route( 'DELETE', $route, $handler );
	}

	/**
	 * Adds a PATCH route.
	 *
	 * @param string $route   The route name.
	 * @param mixed  $handler The route handler.
	 */
	public function patch( $route, $handler ) {
		$this->route( 'PATCH', $route, $handler );
	}

	/**
	 * Adds a HEAD route.
	 *
	 * @param string $route   The route name.
	 * @param mixed  $handler The route handler.
	 */
	public function head( $route, $handler ) {
		$this->route( 'HEAD', $route, $handler );
	}
}
