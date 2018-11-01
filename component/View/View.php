<?php

namespace AweBooking\Component\View;

class View implements \ArrayAccess {
	/**
	 * The view factory instance.
	 *
	 * @var \AweBooking\Component\View\Factory
	 */
	protected $factory;

	/**
	 * The engine implementation.
	 *
	 * @var \AweBooking\Component\View\Engine
	 */
	protected $engine;

	/**
	 * The name of the view.
	 *
	 * @var string
	 */
	protected $view;

	/**
	 * The array of view data.
	 *
	 * @var array
	 */
	protected $data;

	/**
	 * The path to the view file.
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * Create a new view instance.
	 *
	 * @param  \AweBooking\Component\View\Factory $factory
	 * @param  \AweBooking\Component\View\Engine  $engine
	 * @param  string                             $view
	 * @param  string                             $path
	 * @param  mixed                              $data
	 * @return void
	 */
	public function __construct( Factory $factory, Engine $engine, $view, $path, $data = [] ) {
		$this->factory = $factory;
		$this->engine  = $engine;
		$this->view    = $view;
		$this->path    = $path;
		$this->data    = (array) $data;
	}

	/**
	 * Get the string contents of the view.
	 *
	 * @param  callable|null $callback
	 * @return string
	 *
	 * @throws mixed
	 */
	public function render( callable $callback = null ) {
		$contents = $this->get_contents();

		$response = isset( $callback ) ? $callback( $this, $contents ) : null;

		return ! is_null( $response ) ? $response : $contents;
	}

	/**
	 * Get the evaluated contents of the view.
	 *
	 * @return string
	 */
	protected function get_contents() {
		return $this->engine->get( $this->path, $this->gather_data() );
	}

	/**
	 * Get the data bound to the view instance.
	 *
	 * @return array
	 */
	protected function gather_data() {
		return array_merge( $this->factory->get_shared(), $this->data );
	}

	/**
	 * Add a piece of data to the view.
	 *
	 * @param  string|array $key
	 * @param  mixed        $value
	 * @return $this
	 */
	public function with( $key, $value = null ) {
		if ( is_array( $key ) ) {
			$this->data = array_merge( $this->data, $key );
		} else {
			$this->data[ $key ] = $value;
		}

		return $this;
	}

	/**
	 * Add a view instance to the view data.
	 *
	 * @param  string $key
	 * @param  string $view
	 * @param  array  $data
	 * @return $this
	 */
	public function nest( $key, $view, array $data = [] ) {
		return $this->with( $key, $this->factory->make( $view, $data ) );
	}

	/**
	 * Get the name of the view.
	 *
	 * @return string
	 */
	public function name() {
		return $this->get_name();
	}

	/**
	 * Get the name of the view.
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->view;
	}

	/**
	 * Get the array of view data.
	 *
	 * @return array
	 */
	public function get_data() {
		return $this->data;
	}

	/**
	 * Get the path to the view file.
	 *
	 * @return string
	 */
	public function get_path() {
		return $this->path;
	}

	/**
	 * Set the path to the view.
	 *
	 * @param  string $path
	 * @return void
	 */
	public function set_path( $path ) {
		$this->path = $path;
	}

	/**
	 * Get the view factory instance.
	 *
	 * @return \AweBooking\Component\View\Factory
	 */
	public function get_factory() {
		return $this->factory;
	}

	/**
	 * Get the view's rendering engine.
	 *
	 * @return \AweBooking\Component\View\Engine
	 */
	public function get_engine() {
		return $this->engine;
	}

	/**
	 * Determine if a piece of data is bound.
	 *
	 * @param  string $key
	 * @return bool
	 */
	public function offsetExists( $key ) {
		return array_key_exists( $key, $this->data );
	}

	/**
	 * Get a piece of bound data to the view.
	 *
	 * @param  string $key
	 * @return mixed
	 */
	public function offsetGet( $key ) {
		return $this->data[ $key ];
	}

	/**
	 * Set a piece of data on the view.
	 *
	 * @param  string $key
	 * @param  mixed  $value
	 * @return void
	 */
	public function offsetSet( $key, $value ) {
		$this->with( $key, $value );
	}

	/**
	 * Unset a piece of data from the view.
	 *
	 * @param  string $key
	 * @return void
	 */
	public function offsetUnset( $key ) {
		unset( $this->data[ $key ] );
	}

	/**
	 * Get a piece of data from the view.
	 *
	 * @param  string $key
	 * @return mixed
	 */
	public function &__get( $key ) {
		return $this->data[ $key ];
	}

	/**
	 * Set a piece of data on the view.
	 *
	 * @param  string $key
	 * @param  mixed  $value
	 * @return void
	 */
	public function __set( $key, $value ) {
		$this->with( $key, $value );
	}

	/**
	 * Check if a piece of data is bound to the view.
	 *
	 * @param  string $key
	 * @return bool
	 */
	public function __isset( $key ) {
		return isset( $this->data[ $key ] );
	}

	/**
	 * Remove a piece of bound data from the view.
	 *
	 * @param  string $key
	 * @return void
	 */
	public function __unset( $key ) {
		unset( $this->data[ $key ] );
	}

	/**
	 * Get the string contents of the view.
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->render();
	}
}
