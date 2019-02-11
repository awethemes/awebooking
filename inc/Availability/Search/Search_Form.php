<?php

namespace AweBooking\Availability\Search;

use AweBooking\Availability\Request;
use WPLibs\Http\Request as Http_Request;

abstract class Search_Form {
	/**
	 * The res-request instance.
	 *
	 * @var \AweBooking\Availability\Request
	 */
	protected $request;

	/**
	 * The HTTP request instance.
	 *
	 * @var \WPLibs\Http\Request
	 */
	protected $http_request;

	/**
	 * Store current instance number.
	 *
	 * @var int
	 */
	protected $instance = 0;

	/**
	 * Store count instance number.
	 *
	 * @var int
	 */
	protected static $instances = 0;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->instance = ++static::$instances;
	}

	/**
	 * Render search form HTML.
	 *
	 * @return string
	 */
	abstract public function render();

	/**
	 * Returns the instance number.
	 *
	 * @return int
	 */
	public function get_instance_number() {
		return $this->instance;
	}

	/**
	 * Reset the instance number for the testing purpose.
	 *
	 * @access private
	 */
	public static function reset_instance_number() {
		static::$instances = 0;
	}

	/**
	 * Get the search form parameter.
	 *
	 * @param string $key
	 * @return mixed|null
	 */
	public function parameter( $key ) {
		return $this->request->get_parameter( $key );
	}

	/**
	 * Get the request parameter.
	 *
	 * @param string $key
	 * @return mixed|null
	 */
	public function request( $key ) {
		if ( ! $this->http_request ) {
			return null;
		}

		$value = $this->http_request->get( $key );

		return ! is_null( $value ) ? abrs_clean( $value ) : null;
	}

	/**
	 * Returns res-request instance.
	 *
	 * @return \AweBooking\Availability\Request
	 */
	public function get_request() {
		return $this->request;
	}

	/**
	 * Sets the res request implementation.
	 *
	 * @param \AweBooking\Availability\Request $request
	 * @return void
	 */
	public function set_request( Request $request ) {
		$this->request = $request;
	}

	/**
	 * Returns the HTTP request instance.
	 *
	 * @return \WPLibs\Http\Request
	 */
	public function get_http_request() {
		return $this->http_request;
	}

	/**
	 * Set the HTTP request implementation.
	 *
	 * @param Http_Request $http_request
	 * @return void
	 */
	public function set_http_request( Http_Request $http_request ) {
		$this->http_request = $http_request;
	}

	/**
	 * Handle dynamic calls to the search_form.
	 *
	 * @param  string $name       The method name.
	 * @param  array  $parameters The method parameters.
	 * @return $this
	 *
	 * @throws \BadMethodCallException
	 */
	public function __call( $name, $parameters ) {
		if ( method_exists( $this->request, $name ) ) {
			return $this->request->{$name}( ...$parameters );
		}

		throw new \BadMethodCallException( sprintf( 'Method %s::%s does not exist.', get_class( $this->request ), $name ) );
	}

	/**
	 * Increment instance number when clone object.
	 *
	 * @return void
	 */
	public function __clone() {
		$this->instance = ++static::$instances;
		$this->request  = clone $this->request;
	}
}
