<?php
/**
 * Skeleton - The last WordPress framework with everything you'll ever need.
 *
 * @author    awethemes <http://awethemes.com>
 * @link      https://github.com/awethemes/skeleton
 * @package   Skeleton
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// First, load the bootstrap file.
require_once trailingslashit( __DIR__ ) . 'bootstrap.php';

// Next, load CMB2. Don't worry about duplicate,
// CMB2 already take care about that.
require_once trailingslashit( __DIR__ ) . 'libs/cmb2/init.php';

// Now boot the Skeleton after WP-init.
if ( ! defined( 'AWETHEMES_SKELETON_LOADED' ) ) {
	$skeleton = new Skeleton\Skeleton;

	/**
	 * Hooks: skeleton/booting
	 *
	 * @param Skeleton $skeleton
	 */
	do_action( 'skeleton/booting', $skeleton );

	/**
	 * Finally, we run Skeleton after `cmb2_init` fired.
	 *
	 * @hook skeleton/init
	 * @hook skeleton/after_init
	 */
	add_action( 'cmb2_init', array( $skeleton, 'run' ), 5 );

	// Declare Skeleton is loaded.
	define( 'AWETHEMES_SKELETON_LOADED', true );
}
