<?php

// We require our framework if needed.
if ( ! defined( 'SKELETON_LOADED' ) ) {
	if ( file_exists( __DIR__ . '/skeleton/skeleton.php' ) ) {
		require_once trailingslashit( __DIR__ ) . '/skeleton/skeleton.php';
	} else if ( file_exists( __DIR__ . '/vendor/awethemes/skeleton/skeleton.php' ) ) {
		require_once trailingslashit( __DIR__ ) . '/vendor/awethemes/skeleton/skeleton.php';
	} else {
		wp_die( '<h1>Something went wrong!</h1> <p>AweBooking can\'t works without the Skeleton. Please double-check that everything is setup correctly!</p>' );
	}
}

require_once trailingslashit( __DIR__ ) . '/vendor/WebDevStudios/Taxonomy_Single_Term/class.taxonomy-single-term.php';
require trailingslashit( __DIR__ ) . '/inc/functions.php';

skeleton_psr4_autoloader( 'AweBooking\\', trailingslashit( __DIR__ ) . 'inc/' );

// Make AweBooking\AweBooking as AweBooking alias.
class_alias( 'AweBooking\\AweBooking', 'AweBooking' );
