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
	 * @param  bool   $is_ssl     Force the SSL in return URL.
	 * @return string
	 */
	public function route( $path = '/', $parameters = [], $is_ssl = null ) {
		if ( empty( $path ) ) {
			$path = '/';
		}

		// If scheme not provide, guest by is_ssl().
		$scheme = ( is_null( $is_ssl ) && is_ssl() ) ? 'https' : 'http';

		// Force to https.
		if ( 'http' === $scheme && true === $is_ssl ) {
			$scheme = 'https';
		}

		if ( get_option( 'permalink_structure' ) ) {
			global $wp_rewrite;

			if ( $wp_rewrite->using_index_permalinks() ) {
				$url = home_url( $wp_rewrite->index . '/' . $this->plugin->endpoint_name(), $scheme );
			} else {
				$url = home_url( $this->plugin->endpoint_name(), $scheme );
			}

			$url .= '/' . ltrim( $path, '/' );
		} else {
			$url = trailingslashit( home_url( '', $scheme ) );

			// Nginx only allows HTTP/1.0 methods when redirecting from / to /index.php
			// To work around this, we manually add index.php to the URL, avoiding the redirect.
			if ( 'index.php' !== substr( $url, 9 ) ) {
				$url .= 'index.php';
			}

			$path = '/' . ltrim( $path, '/' );

			$url = add_query_arg( 'awebooking_route', $path, $url );
		}

		// Add the additional parameters.
		if ( $parameters ) {
			$url = add_query_arg( $parameters, $url );
		}

		return apply_filters( 'abrs_route_url', rawurldecode( $url ), $path, $parameters, $scheme );
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

		// http://awebooking.com/wp-admin/admin.php?awebooking=/example-path.
		$url = add_query_arg( 'awebooking', $path, admin_url( 'admin.php' ) );

		// Add the additional parameters.
		if ( $parameters ) {
			$url = add_query_arg( $parameters, $url );
		}

		return apply_filters( 'abrs_admin_route_url', rawurldecode( $url ), $path, $parameters );
	}
}
