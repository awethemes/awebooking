<?php

namespace AweBooking\Component\View;

use Illuminate\Support\Arr;

class Factory {
	/**
	 * The engine implementation.
	 *
	 * @var \AweBooking\Component\View\Engine_Resolver
	 */
	protected $engines;

	/**
	 * The view finder implementation.
	 *
	 * @var \AweBooking\Calendar\Finder\Finder
	 */
	protected $finder;

	/**
	 * Data that should be available to all templates.
	 *
	 * @var array
	 */
	protected $shared = [];

	/**
	 * The extension to engine bindings.
	 *
	 * @var array
	 */
	protected $extensions = [
		'twig' => 'twig',
		'php'  => 'php',
	];

	/**
	 * Create a new view factory instance.
	 *
	 * @param \AweBooking\Component\View\Engine_Resolver $engines
	 * @param \AweBooking\Component\View\Finder          $finder
	 */
	public function __construct( Engine_Resolver $engines, Finder $finder ) {
		$this->engines = $engines;

		$this->finder  = $finder;

		$this->share( '__env', $this );
	}

	/**
	 * Get the evaluated view contents for the given view.
	 *
	 * @param  string $path
	 * @param  array  $data
	 * @return \AweBooking\Component\View\View
	 */
	public function file( $path, $data = [] ) {
		return $this->view_instance( $path, $path, $data );
	}

	/**
	 * Get the evaluated view contents for the given view.
	 *
	 * @param  string $view
	 * @param  array  $data
	 * @return \AweBooking\Component\View\View
	 */
	public function make( $view, $data = [] ) {
		$path = $this->finder->find(
			$view = $this->normalize_name( $view )
		);

		return $this->view_instance( $view, $path, $data );
	}

	/**
	 * Get the rendered contents of a partial from a loop.
	 *
	 * @param  string $view
	 * @param  array  $data
	 * @param  string $iterator
	 * @param  string $empty
	 * @return string
	 */
	public function render_each( $view, $data, $iterator, $empty = '' ) {
		$result = '';

		// If is actually data in the array, we will loop through the data and append
		// an instance of the partial view to the final result HTML passing in the
		// iterated value of this data array, allowing the views to access them.
		if ( count( $data ) > 0 ) {
			foreach ( $data as $key => $value ) {
				// @codingStandardsIgnoreLine
				$result .= $this->make( $view, [ 'key' => $key, $iterator => $value ] )->render();
			}
		} else {
			// If there is no data in the array, we will render the contents of the empty view.
			$result = $empty ? $this->make( $empty )->render() : '';
		}

		return $result;
	}

	/**
	 * Normalize a view name.
	 *
	 * @param  string $name
	 * @return string
	 */
	protected function normalize_name( $name ) {
		$delimiter = Finder::HINT_PATH_DELIMITER;

		if ( strpos( $name, $delimiter ) === false ) {
			return str_replace( '/', '.', $name );
		}

		list( $namespace, $name ) = explode( $delimiter, $name );

		return $namespace . $delimiter . str_replace( '/', '.', $name );
	}

	/**
	 * Create a new view instance from the given arguments.
	 *
	 * @param  string $view
	 * @param  string $path
	 * @param  array  $data
	 * @return \AweBooking\Component\View\View
	 */
	protected function view_instance( $view, $path, $data ) {
		return new View( $this, $this->get_engine_from_path( $path ), $view, $path, $data );
	}

	/**
	 * Determine if a given view exists.
	 *
	 * @param  string $view
	 * @return bool
	 */
	public function exists( $view ) {
		try {
			$this->finder->find( $view );
		} catch ( \InvalidArgumentException $e ) {
			return false;
		}

		return true;
	}

	/**
	 * Get the appropriate view engine for the given path.
	 *
	 * @param  string $path
	 * @return \AweBooking\Component\View\Engine
	 *
	 * @throws \InvalidArgumentException
	 */
	public function get_engine_from_path( $path ) {
		if ( ! $extension = $this->get_extension( $path ) ) {
			throw new \InvalidArgumentException( "Unrecognized extension in file: $path" );
		}

		$engine = $this->extensions[ $extension ];

		return $this->engines->resolve( $engine );
	}

	/**
	 * Get the extension used by the view file.
	 *
	 * @param  string $path
	 * @return string
	 */
	protected function get_extension( $path ) {
		$extensions = array_keys( $this->extensions );

		$extension = pathinfo( $path, PATHINFO_EXTENSION );

		foreach ( $extensions as $_extension ) {
			if ( $extension === $_extension ) {
				return $_extension;
			}
		}

		return null;
	}

	/**
	 * Add a piece of shared data to the environment.
	 *
	 * @param  array|string $key
	 * @param  mixed        $value
	 * @return void
	 */
	public function share( $key, $value = null ) {
		$keys = is_array( $key ) ? $key : [ $key => $value ];

		foreach ( $keys as $_key => $_value ) {
			$this->shared[ $_key ] = $_value;
		}
	}

	/**
	 * Add a location to the array of view locations.
	 *
	 * @param  string $location
	 * @return void
	 */
	public function add_location( $location ) {
		$this->finder->add_location( $location );
	}

	/**
	 * Add a new namespace to the loader.
	 *
	 * @param  string       $namespace
	 * @param  string|array $hints
	 * @return $this
	 */
	public function add_namespace( $namespace, $hints ) {
		$this->finder->add_namespace( $namespace, $hints );

		return $this;
	}

	/**
	 * Prepend a new namespace to the loader.
	 *
	 * @param  string       $namespace
	 * @param  string|array $hints
	 * @return $this
	 */
	public function prepend_namespace( $namespace, $hints ) {
		$this->finder->prepend_namespace( $namespace, $hints );

		return $this;
	}

	/**
	 * Replace the namespace hints for the given namespace.
	 *
	 * @param  string       $namespace
	 * @param  string|array $hints
	 * @return $this
	 */
	public function replace_namespace( $namespace, $hints ) {
		$this->finder->replace_namespace( $namespace, $hints );

		return $this;
	}

	/**
	 * Register a valid view extension and its engine.
	 *
	 * @param  string   $extension
	 * @param  string   $engine
	 * @param  \Closure $resolver
	 * @return void
	 */
	public function add_extension( $extension, $engine, $resolver = null ) {
		$this->finder->add_extension( $extension );

		if ( $resolver ) {
			$this->engines->register( $engine, $resolver );
		}

		unset( $this->extensions[ $extension ] );

		$this->extensions = array_merge( [ $extension => $engine ], $this->extensions );
	}

	/**
	 * Get the extension to engine bindings.
	 *
	 * @return array
	 */
	public function get_extensions() {
		return $this->extensions;
	}

	/**
	 * Get the engine resolver instance.
	 *
	 * @return \AweBooking\Component\View\Engine_Resolver
	 */
	public function get_engine_resolver() {
		return $this->engines;
	}

	/**
	 * Get the view finder instance.
	 *
	 * @return \AweBooking\Component\View\Finder
	 */
	public function get_finder() {
		return $this->finder;
	}

	/**
	 * Set the view finder instance.
	 *
	 * @param  \AweBooking\Component\View\Finder $finder
	 * @return void
	 */
	public function set_finder( Finder $finder ) {
		$this->finder = $finder;
	}

	/**
	 * Flush the cache of views located by the finder.
	 *
	 * @return void
	 */
	public function flush_finder_cache() {
		$this->get_finder()->flush();
	}

	/**
	 * Get an item from the shared data.
	 *
	 * @param  string $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function shared( $key, $default = null ) {
		return Arr::get( $this->shared, $key, $default );
	}

	/**
	 * Get all of the shared data for the environment.
	 *
	 * @return array
	 */
	public function get_shared() {
		return $this->shared;
	}
}
