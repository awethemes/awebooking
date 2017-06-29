<?php
namespace Skeleton\Http;

use Skeleton\Skeleton;
use Skeleton\Support\Multidimensional;

class Http_Request {
	/**
	 * The globally available instance of the skeleton container.
	 *
	 * @var Skeleton
	 */
	protected $skeleton;

	/**
	 * Constructor.
	 *
	 * @param Skeleton $skeleton The skeleton container instance.
	 */
	public function __construct( Skeleton $skeleton ) {
		$this->skeleton = $skeleton;
	}

	/**
	 * Get a input value by key with multidimensional support.
	 *
	 * @param  string $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function input( $key, $default = null ) {
		return Multidimensional::find( $_REQUEST, $key, $default );
	}

	/**
	 * Get a input value via HTTP-GET request.
	 *
	 * @param  string $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function get( $key, $default = null ) {
		return Multidimensional::find( $_GET, $key, $default );
	}

	/**
	 * Get a input value via HTTP-POST request.
	 *
	 * @param  string $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function post( $key, $default = null ) {
		// @codingStandardsIgnoreLine
		return Multidimensional::find( $_POST, $key, $default );
	}

	/**
	 * Determines whether the current request is a WordPress Ajax request.
	 *
	 * @return boolean
	 */
	public function is_ajax() {
		if ( function_exists( 'wp_doing_ajax' ) ) {
			return wp_doing_ajax();
		}

		return defined( 'DOING_AJAX' ) && DOING_AJAX;
	}
}
