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

WP_Session::get_instance();

// Skeleton Support.
skeleton()->trigger( new AweBooking\Skeleton_Hooks );

// Make AweBooking\AweBooking as AweBooking alias.
class_alias( 'AweBooking\\AweBooking', 'AweBooking' );

/**
 * Prints an update nag after an unsuccessful attempt to active
 * AweBooking on WordPress versions prior to 4.6.
 *
 * @global string $wp_version WordPress version.
 */
function awebooking_wordpress_upgrade_notice() {
	$message = sprintf( esc_html__( 'AweBooking requires at least WordPress version 4.6, you are running version %s. Please upgrade and try again!', 'awebooking' ), $GLOBALS['wp_version'] );
	printf( '<div class="error"><p>%s</p></div>', $message ); // WPCS: XSS OK.

	deactivate_plugins( array( 'awebooking/awebooking.php' ) );
}

/**
 * Adds a message for outdate PHP version.
 */
function awebooking_php_upgrade_notice() {
	$message = sprintf( esc_html__( 'AweBooking requires at least PHP version 5.6.4 to works, you are running version %s. Please contact to your administrator to upgrade PHP version!', 'awebooking' ), phpversion() );
	printf( '<div class="error"><p>%s</p></div>', $message ); // WPCS: XSS OK.

	deactivate_plugins( array( 'awebooking/awebooking.php' ) );
}
