<?php

use Composer\Autoload\ClassLoader;

if ( ! function_exists( 'skeleton_psr4_autoloader' ) ) :
	/**
	 * Register PSR-4 autoload classess.
	 *
	 * @param  string|array $namespace A string of namespace or an array with
	 *                                 namespace and directory to autoload.
	 * @param  string       $base_dir  Autoload directory if $namespace is string.
	 * @return void
	 */
	function skeleton_psr4_autoloader( $namespace, $base_dir = null ) {
		$loader = new ClassLoader;

		if ( is_string( $namespace ) && $base_dir ) {
			$loader->setPsr4( rtrim( $namespace, '\\' ) . '\\', $base_dir );
		} elseif ( is_array( $namespace ) ) {
			foreach ( $namespace as $prefix => $dir ) {
				$loader->setPsr4( rtrim( $prefix, '\\' ) . '\\', $dir );
			}
		}

		$loader->register( true );
	}
endif;
