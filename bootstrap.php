<?php
/**
 * AweBooking bootstrap file.
 *
 * @package AweBooking
 */

/**
 * We need autoload via Composer to make everything works.
 */
require trailingslashit( __DIR__ ) . 'vendor/autoload.php';

// Try locate the Skeleton.
if ( ! defined( 'AWETHEMES_SKELETON_LOADED' ) ) {
	if ( file_exists( __DIR__ . '/skeleton/skeleton.php' ) ) {
		require_once trailingslashit( __DIR__ ) . '/skeleton/skeleton.php';
	} elseif ( file_exists( __DIR__ . '/vendor/awethemes/skeleton/skeleton.php' ) ) {
		require_once trailingslashit( __DIR__ ) . '/vendor/awethemes/skeleton/skeleton.php';
	} else {
		wp_die( '<h1>Something went wrong!</h1> <p>AweBooking can\'t works without the Skeleton. Please double-check that everything is setup correctly!</p>' );
	}
}

require_once trailingslashit( __DIR__ ) . 'inc/functions.php';
require_once trailingslashit( __DIR__ ) . 'inc/template-functions.php';

// Make AweBooking\AweBooking as AweBooking alias.
class_alias( 'AweBooking\\AweBooking', 'AweBooking' );

// TODO: Remove this in next version.
class_alias( 'AweBooking\Support\WP_Object', 'AweBooking\Model\WP_Object' );
class_alias( 'AweBooking\Support\Service_Hooks', 'Skeleton\Container\Service_Hooks' );

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
		$loader = new Composer\Autoload\ClassLoader;

		if ( is_string( $namespace ) && $base_dir ) {
			$loader->setPsr4( rtrim($namespace, '\\') . '\\', $base_dir );
		} elseif ( is_array( $namespace ) ) {
			foreach ( $namespace as $prefix => $dir ) {
				$loader->setPsr4( rtrim($prefix, '\\') . '\\', $dir );
			}
		}

		$loader->register( true );
	}
endif;
