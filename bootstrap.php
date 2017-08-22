<?php
/**
 * AweBooking bootstrap file.
 *
 * @package AweBooking
 */

// We require our framework if needed.
if ( ! defined( 'SKELETON_LOADED' ) ) {
	if ( file_exists( __DIR__ . '/skeleton/skeleton.php' ) ) {
		require_once trailingslashit( __DIR__ ) . '/skeleton/skeleton.php';
	} elseif ( file_exists( __DIR__ . '/vendor/awethemes/skeleton/skeleton.php' ) ) {
		require_once trailingslashit( __DIR__ ) . '/vendor/awethemes/skeleton/skeleton.php';
	} else {
		wp_die( '<h1>Something went wrong!</h1> <p>AweBooking can\'t works without the Skeleton. Please double-check that everything is setup correctly!</p>' );
	}
}

require_once trailingslashit( __DIR__ ) . 'vendor/ericmann/wp-session-manager/wp-session-manager.php';
require_once trailingslashit( __DIR__ ) . 'vendor/webdevstudios/taxonomy_single_term/class.taxonomy-single-term.php';

require_once trailingslashit( __DIR__ ) . 'inc/functions.php';
require_once trailingslashit( __DIR__ ) . 'inc/template-functions.php';

// Make AweBooking\AweBooking as AweBooking alias.
class_alias( 'AweBooking\\AweBooking', 'AweBooking' );

// Skeleton Support.
skeleton()->trigger( new AweBooking\Support\Skeleton_Support );
