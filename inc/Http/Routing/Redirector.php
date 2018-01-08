<?php
namespace AweBooking\Http\Routing;

use Awethemes\WP_Session\Session;
use Awethemes\Http\Redirect_Response;

class Redirector {
	/**
	 * The URL generator instance.
	 *
	 * @var Url_Generator
	 */
	protected $generator;

	/**
	 * The session store instance.
	 *
	 * @var Awethemes\WP_Session\Session
	 */
	protected $session;

	/**
	 * Create a new Redirector instance.
	 *
	 * @param  Url_Generator $generator The Url_Generator.
	 * @return void
	 */
	public function __construct( Url_Generator $generator ) {
		$this->generator = $generator;
	}

	/**
	 * Set the active session store.
	 *
	 * @param  \Awethemes\WP_Session\Session $session The session store.
	 * @return void
	 */
	public function set_wp_session( Session $session ) {
		$this->session = $session;
	}

	/**
	 * Get the URL generator instance.
	 *
	 * @return Url_Generator
	 */
	public function get_url_generator() {
		return $this->generator;
	}

	/**
	 * Create a new redirect response to the given url.
	 *
	 * @param  string $url           The url redirect to.
	 * @param  int    $status        The response status code.
	 * @param  array  $headers       The response headers.
	 * @param  bool   $safe_redirect Use safe redirect or not.
	 * @return \AweBooking\Http\Redirect_Response
	 */
	public function to( $url, $status = 302, $headers = [], $safe_redirect = false ) {
		return $this->create_redirect( $this->generator->to( $url ), $status, $headers, $safe_redirect );
	}

	/**
	 * Create a new redirect response to the "home".
	 *
	 * @param  int $status The response status code.
	 * @return \AweBooking\Http\Redirect_Response
	 */
	public function home( $status = 302 ) {
		return $this->create_redirect( home_url(), $status, [], true );
	}

	/**
	 * Create a new redirect response to the "admin" area.
	 *
	 * @param  string $path    Optional path relative to the admin URL.
	 * @param  int    $status  The response status code.
	 * @param  array  $headers The response headers.
	 * @return \AweBooking\Http\Redirect_Response
	 */
	public function admin( $path = '', $status = 302, $headers = [] ) {
		return $this->create_redirect( admin_url( $path ), $status, $headers, true );
	}

	/**
	 * Create a new redirect response to the previous location.
	 *
	 * @param  mixed $fallback The fallback, if null it'll be admin_url() or home_url() depend by context.
	 * @param  int   $status   The response status code.
	 * @param  array $headers  The response headers.
	 * @return \AweBooking\Http\Redirect_Response
	 */
	public function back( $fallback = null, $status = 302, $headers = [] ) {
		$previous = wp_get_referer();

		if ( ! $previous && ! $fallback ) {
			$fallback = is_admin() ? admin_url() : home_url();
		}

		return $this->to( $previous ?: $fallback, $status, $headers, true );
	}

	/**
	 * Create a new redirect response to a public route area with a path.
	 *
	 * @param  string $path       The route path.
	 * @param  array  $parameters The additional parameters.
	 * @param  int    $status     The response status code.
	 * @param  array  $headers    The response headers.
	 * @return \AweBooking\Http\Redirect_Response
	 */
	public function route( $path = '/', $parameters = [], $status = 302, $headers = [] ) {
		$to_url = $this->generator->route( $path );

		if ( $parameters ) {
			$to_url = add_query_arg( $parameters, $to_url );
		}

		return $this->create_redirect( $to_url, $status, $headers, true );
	}

	/**
	 * Create a new redirect response to a admin route area with a path.
	 *
	 * @param  string $path       The route path.
	 * @param  array  $parameters The additional parameters.
	 * @param  int    $status     The response status code.
	 * @param  array  $headers    The response headers.
	 * @return \AweBooking\Http\Redirect_Response
	 */
	public function admin_route( $path = '/', $parameters = [], $status = 302, $headers = [] ) {
		$to_url = $this->generator->admin_route( $path );

		if ( $parameters ) {
			$to_url = add_query_arg( $parameters, $to_url );
		}

		return $this->create_redirect( $to_url, $status, $headers, true );
	}

	/**
	 * Create a new redirect response.
	 *
	 * @param  string $url           The url redirect to.
	 * @param  int    $status        The response status code.
	 * @param  array  $headers       The response headers.
	 * @param  bool   $safe_redirect Use safe redirect or not.
	 * @return \AweBooking\Http\Redirect_Response
	 */
	protected function create_redirect( $url, $status, $headers, $safe_redirect ) {
		$redirect = new Redirect_Response( $url, $status, $headers, $safe_redirect );

		$redirect->set_request( $this->generator->get_request() );

		if ( isset( $this->session ) ) {
			$redirect->set_session( $this->session );
		}

		return $redirect;
	}
}
