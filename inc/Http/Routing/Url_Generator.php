<?php
namespace AweBooking\Http\Routing;

use AweBooking\AweBooking;
use Awethemes\Http\Request;

class Url_Generator {
	/**
	 * The AweBooking instance.
	 *
	 * @var \AweBooking\AweBooking
	 */
	protected $awebooking;

	/**
	 * Constructor.
	 *
	 * @param AweBooking $awebooking awebooking The AweBooking instance.
	 */
	public function __construct( AweBooking $awebooking ) {
		$this->awebooking = $awebooking;
	}

	/**
	 * Get the request instance.
	 *
	 * @return \Awethemes\Http\Request
	 */
	public function get_request() {
		return $this->awebooking->make( 'request' );
	}

	/**
	 * Get the current URL (full) for the request.
	 *
	 * @return string
	 */
	public function full() {
		return $this->get_request()->full_url();
	}

	/**
	 * Get the current URL for the request.
	 *
	 * @return string
	 */
	public function current() {
		return $this->get_request()->url();
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
		if ( $this->is_valid_url( $path ) ) {
			return $path;
		}

		$url = home_url( $path, $scheme );

		if ( ! empty( $parameters ) ) {
			$url = add_query_arg( $parameters, $url );
		}

		return $url;
	}

	/**
	 * Generate a url for the admin.
	 *
	 * @param  string $path       The path relative to the admin URL.
	 * @param  array  $parameters The additional parameters.
	 * @param  string $scheme     Optional. Sanitization scheme, default: 'admin'.
	 * @return string
	 */
	public function admin( $path = '', $parameters = [], $scheme = 'admin' ) {
		$admin_url = admin_url( $path, $scheme );

		if ( ! empty( $parameters ) ) {
			$admin_url = add_query_arg( $parameters, $admin_url );
		}

		return $admin_url;
	}

	/**
	 * Retrieves the site route URL.
	 *
	 * @param  string $path    Optional. The route. Default '/'.
	 * @param  string $scheme  Optional. Sanitization scheme. Default 'null depend on is_ssl()'.
	 * @return string
	 */
	public function route( $path = '/', $scheme = null ) {
		if ( empty( $path ) ) {
			$path = '/';
		}

		// If scheme not provide, guest by is_ssl().
		$scheme = $scheme ? $scheme : ( is_ssl() ? 'https' : 'http' );

		if ( get_option( 'permalink_structure' ) ) {
			global $wp_rewrite;

			if ( $wp_rewrite->using_index_permalinks() ) {
				$url = home_url( $wp_rewrite->index . '/' . $this->awebooking->endpoint_name(), $scheme );
			} else {
				$url = home_url( $this->awebooking->endpoint_name(), $scheme );
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

		return apply_filters( 'awebooking/route_url', $url, $path, $scheme );
	}

	/**
	 * Retrieves the admin route URL.
	 *
	 * @param  string $path   Optional. The admin route. Default '/'.
	 * @param  string $scheme Optional. Sanitization scheme. Default 'admin'.
	 * @return string
	 */
	public function admin_route( $path = '/', $scheme = 'admin' ) {
		if ( empty( $path ) ) {
			$path = '/';
		}

		$path = '/' . ltrim( $path, '/' );

		// http://awebooking.com/wp-admin/admin.php?awebooking=/example-path.
		$url = add_query_arg( 'awebooking', $path, admin_url( 'admin.php', $scheme ) );

		return apply_filters( 'awebooking/admin_route_url', $url, $path );
	}

	/**
	 * Get the check availability page url (fallback to home-url).
	 *
	 * @param  array $parameters The additional parameters.
	 * @return string
	 */
	public function availability_page( $parameters = [] ) {
		$permalink = $this->get_page_permalink( 'check_availability' );

		if ( ! empty( $parameters ) ) {
			$permalink = add_query_arg( $parameters, $permalink );
		}

		return $permalink;
	}

	/**
	 * Get the booking page url (fallback to home-url).
	 *
	 * @param  array $parameters The additional parameters.
	 * @return string
	 */
	public function booking_page( $parameters = [] ) {
		$permalink = $this->get_page_permalink( 'booking' );

		if ( ! empty( $parameters ) ) {
			$permalink = add_query_arg( $parameters, $permalink );
		}

		return $permalink;
	}

	/**
	 * Get the checkout page url (fallback to home-url).
	 *
	 * @param  array $parameters The additional parameters.
	 * @return string
	 */
	public function checkout_page( $parameters = [] ) {
		$permalink = $this->get_page_permalink( 'checkout' );

		if ( ! empty( $parameters ) ) {
			$permalink = add_query_arg( $parameters, $permalink );
		}

		return $permalink;
	}

	/**
	 * Retrieve page permalink by setting name.
	 *
	 * @param  string $page The retrieve page.
	 * @return string
	 */
	protected function get_page_permalink( $page ) {
		$page_id = apply_filters( "awebooking/get_{$page}_page_id", $this->awebooking['setting']->get( "page_{$page}" ) );

		$permalink = $page_id ? get_permalink( $page_id ) : get_home_url();

		return apply_filters( "awebooking/get_{$page}_page_permalink", $permalink );
	}

	/**
	 * Determine if the given "path" is a valid URL.
	 *
	 * @param  string $path The input URL to check.
	 * @return bool
	 */
	public function is_valid_url( $path ) {
		if ( ! preg_match( '~^(#|//|https?://|mailto:|tel:)~', $path ) ) {
			return filter_var( $path, FILTER_VALIDATE_URL ) !== false;
		}

		return true;
	}
}
