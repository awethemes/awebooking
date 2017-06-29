<?php
namespace Skeleton\Support;

/**
 * A PSR-4 autoload class.
 *
 * @see http://www.php-fig.org/psr/psr-4/examples/#class-example
 */
class Autoload {
	/**
	 * An associative array where the key is a namespace prefix and the value
	 * is an array of base directories for classes in that namespace.
	 *
	 * @var array
	 */
	protected $prefixes = array();

	/**
	 * Register loader with SPL autoloader stack.
	 *
	 * @return void
	 */
	public function register() {
		spl_autoload_register( array( $this, 'load_class' ) );
	}

	/**
	 * Adds a base directory for a namespace prefix.
	 *
	 * @param string $prefix   The namespace prefix.
	 * @param string $base_dir A base directory for class files in the  namespace.
	 * @param bool   $prepend  If true, prepend the base directory to the stack instead of appending it;
	 *                         this causes it to be searched first rather than last.
	 */
	public function add_namespace( $prefix, $base_dir, $prepend = false ) {
		// Normalize namespace prefix.
		$prefix = trim( $prefix, '\\' ) . '\\';

		// Normalize the base directory with a trailing separator.
		$base_dir = rtrim( $base_dir, DIRECTORY_SEPARATOR ) . '/';

		// Initialize the namespace prefix array.
		if ( isset( $this->prefixes[ $prefix ] ) === false ) {
			$this->prefixes[ $prefix ] = array();
		}

		// Retain the base directory for the namespace prefix.
		if ( $prepend ) {
			array_unshift( $this->prefixes[ $prefix ], $base_dir );
		} else {
			array_push( $this->prefixes[ $prefix ], $base_dir );
		}
	}

	/**
	 * Loads the class file for a given class name.
	 *
	 * @param string $class The fully-qualified class name.
	 * @return mixed The mapped file name on success, or boolean false on
	 * failure.
	 */
	public function load_class( $class ) {
		// The current namespace prefix.
		$prefix = $class;

		// Work backwards through the namespace names of the fully-qualified
		// class name to find a mapped file name.
		while ( false !== $pos = strrpos( $prefix, '\\' ) ) {

			// Retain the trailing namespace separator in the prefix.
			$prefix = substr( $class, 0, $pos + 1 );

			// The rest is the relative class name.
			$relative_class = substr( $class, $pos + 1 );

			// Try to load a mapped file for the prefix and relative class.
			$mapped_file = $this->load_mapped_file( $prefix, $relative_class );
			if ( $mapped_file ) {
				return $mapped_file;
			}

			// remove the trailing namespace separator for the next iteration of strrpos().
			$prefix = rtrim( $prefix, '\\' );
		}

		// Never found a mapped file.
		return false;
	}

	/**
	 * Load the mapped file for a namespace prefix and relative class.
	 *
	 * @param  string $prefix         The namespace prefix.
	 * @param  string $relative_class The relative class name.
	 * @return mixed
	 */
	protected function load_mapped_file( $prefix, $relative_class ) {
		// Are there any base directories for this namespace prefix?
		if ( isset( $this->prefixes[ $prefix ] ) === false ) {
			return false;
		}

		// Look through base directories for this namespace prefix.
		foreach ( $this->prefixes[ $prefix ] as $base_dir ) {

			// Replace the namespace prefix with the base directory,
			// replace namespace separators with directory separators
			// in the relative class name, append with .php.
			$file = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

			// If the mapped file exists, require it.
			if ( $this->require_file( $file ) ) {
				// Yes, we're done.
				return $file;
			}
		}

		// Never found it.
		return false;
	}

	/**
	 * If a file exists, require it from the file system.
	 *
	 * @param string $file The file to require.
	 * @return bool True if the file exists, false if not.
	 */
	protected function require_file( $file ) {
		if ( file_exists( $file ) ) {
			require $file;
			return true;
		}

		return false;
	}
}
