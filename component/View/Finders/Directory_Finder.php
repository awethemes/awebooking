<?php

namespace AweBooking\Component\View\Finders;

use AweBooking\Component\View\Finder;

class Directory_Finder implements Finder {
	/**
	 * The array of active view paths.
	 *
	 * @var array
	 */
	protected $paths;

	/**
	 * The array of views that have been located.
	 *
	 * @var array
	 */
	protected $views = [];

	/**
	 * The namespace to file path hints.
	 *
	 * @var array
	 */
	protected $hints = [];

	/**
	 * Register a view extension with the finder.
	 *
	 * @var array
	 */
	protected $extensions = [ 'twig', 'php' ];

	/**
	 * Create a new file view loader instance.
	 *
	 * @param  array $paths
	 * @param  array $extensions
	 * @return void
	 */
	public function __construct( array $paths, array $extensions = null ) {
		$this->paths = $paths;

		if ( $extensions ) {
			$this->extensions = $extensions;
		}
	}

	/**
	 * Get the fully qualified location of the view.
	 *
	 * @param  string $name
	 * @return string
	 */
	public function find( $name ) {
		$name = trim( $name );

		if ( isset( $this->views[ $name ] ) ) {
			return $this->views[ $name ];
		}

		if ( $this->has_hint_information( $name ) ) {
			return $this->views[ $name ] = $this->find_namespaced_view( $name );
		}

		return $this->views[ $name ] = $this->find_in_paths( $name, $this->paths );
	}

	/**
	 * Get the path to a template with a named path.
	 *
	 * @param  string $name
	 * @return string
	 */
	protected function find_namespaced_view( $name ) {
		list( $namespace, $view ) = $this->parse_namespace_segments( $name );

		return $this->find_in_paths( $view, $this->hints[ $namespace ] );
	}

	/**
	 * Get the segments of a template with a named path.
	 *
	 * @param  string $name
	 * @return array
	 *
	 * @throws \InvalidArgumentException
	 */
	protected function parse_namespace_segments( $name ) {
		$segments = explode( Finder::HINT_PATH_DELIMITER, $name );

		if ( count( $segments ) !== 2 ) {
			throw new \InvalidArgumentException( "View [$name] has an invalid name." );
		}

		if ( ! isset( $this->hints[ $segments[0] ] ) ) {
			throw new \InvalidArgumentException( "No hint path defined for [{$segments[0]}]." );
		}

		return $segments;
	}

	/**
	 * Find the given view in the list of paths.
	 *
	 * @param  string $name
	 * @param  array  $paths
	 * @return string
	 *
	 * @throws \InvalidArgumentException
	 */
	protected function find_in_paths( $name, $paths ) {
		$paths = ! is_array( $paths ) ? [ $paths ] : $paths;

		foreach ( $paths as $path ) {
			foreach ( $this->get_possible_view_files( $name ) as $file ) {
				if ( file_exists( $view_path = $path . '/' . $file ) ) {
					return $view_path;
				}
			}
		}

		throw new \InvalidArgumentException( "View [$name] not found." );
	}

	/**
	 * Get an array of possible view files.
	 *
	 * @param  string $name
	 * @return array
	 */
	protected function get_possible_view_files( $name ) {
		return array_map( function ( $extension ) use ( $name ) {
			return str_replace( '.', '/', $name ) . '.' . $extension;
		}, $this->extensions );
	}

	/**
	 * Add a location to the finder.
	 *
	 * @param  string $location
	 * @return void
	 */
	public function add_location( $location ) {
		$this->paths[] = $location;
	}

	/**
	 * Prepend a location to the finder.
	 *
	 * @param  string $location
	 * @return void
	 */
	public function prepend_location( $location ) {
		array_unshift( $this->paths, $location );
	}

	/**
	 * Add a namespace hint to the finder.
	 *
	 * @param  string       $namespace
	 * @param  string|array $hints
	 * @return void
	 */
	public function add_namespace( $namespace, $hints ) {
		$hints = (array) $hints;

		if ( isset( $this->hints[ $namespace ] ) ) {
			$hints = array_merge( $this->hints[ $namespace ], $hints );
		}

		$this->hints[ $namespace ] = $hints;
	}

	/**
	 * Prepend a namespace hint to the finder.
	 *
	 * @param  string       $namespace
	 * @param  string|array $hints
	 * @return void
	 */
	public function prepend_namespace( $namespace, $hints ) {
		$hints = (array) $hints;

		if ( isset( $this->hints[ $namespace ] ) ) {
			$hints = array_merge( $hints, $this->hints[ $namespace ] );
		}

		$this->hints[ $namespace ] = $hints;
	}

	/**
	 * Replace the namespace hints for the given namespace.
	 *
	 * @param  string       $namespace
	 * @param  string|array $hints
	 * @return void
	 */
	public function replace_namespace( $namespace, $hints ) {
		$this->hints[ $namespace ] = (array) $hints;
	}

	/**
	 * Register an extension with the view finder.
	 *
	 * @param  string $extension
	 * @return void
	 */
	public function add_extension( $extension ) {
		if ( ( $index = array_search( $extension, $this->extensions ) ) !== false ) {
			unset( $this->extensions[ $index ] );
		}

		array_unshift( $this->extensions, $extension );
	}

	/**
	 * Returns whether or not the view name has any hint information.
	 *
	 * @param  string $name
	 * @return bool
	 */
	public function has_hint_information( $name ) {
		return strpos( $name, Finder::HINT_PATH_DELIMITER ) > 0;
	}

	/**
	 * Flush the cache of located views.
	 *
	 * @return void
	 */
	public function flush() {
		$this->views = [];
	}

	/**
	 * Get the active view paths.
	 *
	 * @return array
	 */
	public function get_paths() {
		return $this->paths;
	}

	/**
	 * Get the namespace to file path hints.
	 *
	 * @return array
	 */
	public function get_hints() {
		return $this->hints;
	}

	/**
	 * Get registered extensions.
	 *
	 * @return array
	 */
	public function get_extensions() {
		return $this->extensions;
	}
}
