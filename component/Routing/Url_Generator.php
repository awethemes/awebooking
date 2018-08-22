<?php
namespace AweBooking\Component\Routing;

use AweBooking\Plugin;

class Url_Generator {
	/**
	 * The plugin instance.
	 *
	 * @var \AweBooking\Plugin
	 */
	protected $plugin;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Plugin $plugin The plugin instance.
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Generate a url for the site.
	 *
	 * @param  string $path       The path relative to the home URL.
	 * @param  array  $parameters The additional parameters.
	 * @param  string $scheme     Optional. Sanitization scheme.
	 * @return string
	 */
	public function to( $path = '', $parameters = [], $scheme = null ) {
		if ( abrs_valid_url( $path ) ) {
			return $path;
		}

		$url = home_url( $path, $scheme );

		if ( ! empty( $parameters ) ) {
			$url = add_query_arg( $parameters, $url );
		}

		return rawurldecode( $url );
	}

	/**
	 * Retrieves the site route URL.
	 *
	 * @param  string $path       Optional. The route. Default '/'.
	 * @param  array  $parameters The additional parameters.
	 * @return string
	 */
	public function route( $path = '/', $parameters = [] ) {
		if ( empty( $path ) ) {
			$path = '/';
		} else {
			$path = '/' . ltrim( $path, '/' );
		}

		if ( get_option( 'permalink_structure' ) ) {
			global $wp_rewrite;

			$endpoint_path = ltrim( $this->plugin->endpoint_name(), '/' ) . $path;

			if ( $wp_rewrite->using_index_permalinks() ) {
				$url = home_url( $wp_rewrite->index . '/' . $endpoint_path );
			} else {
				$url = home_url( $endpoint_path );
			}
		} else {
			$url = trailingslashit( home_url( '' ) );

			// Nginx only allows HTTP/1.0 methods when redirecting from / to /index.php
			// To work around this, we manually add index.php to the URL, avoiding the redirect.
			if ( 'index.php' !== substr( $url, 9 ) ) {
				$url .= 'index.php';
			}

			$url = add_query_arg( 'awebooking_route', $path, $url );
		}

		// Add the additional parameters.
		if ( $parameters ) {
			$url = add_query_arg( $parameters, $url );
		}

		return rawurldecode( $url );
	}

	/**
	 * Retrieves the admin route URL.
	 *
	 * @param  string $path       Optional, the admin route.
	 * @param  array  $parameters The additional parameters.
	 * @return string
	 */
	public function admin_route( $path = '/', $parameters = [] ) {
		if ( empty( $path ) ) {
			$path = '/';
		}

		$path = '/' . ltrim( $path, '/' );

		$url = add_query_arg( 'awebooking', $path, admin_url( 'admin.php' ) );

		// Add the additional parameters.
		if ( $parameters ) {
			$url = add_query_arg( $parameters, $url );
		}

		return rawurldecode( $url );
	}
}
