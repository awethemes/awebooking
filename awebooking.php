<?php
/**
 * Plugin Name:     AweBooking
 * Plugin URI:      https://awethemes.com/plugins/awebooking
 * Description:     AweBooking is both simple and powerful when it comes to its purpose: booking hotel room. It allows you to setup any reservations quickly, pleasantly and easily.
 * Author:          Awethemes
 * Author URI:      https://awethemes.com
 * Text Domain:     awebooking
 * Domain Path:     /languages
 * Version:         3.0.0-beta13
 *
 * @package         AweBooking
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * AweBooking only works in WordPress 4.6 or later.
 */
if ( version_compare( $GLOBALS['wp_version'], '4.6', '<' ) ) {
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

	add_action( 'admin_notices', 'awebooking_wordpress_upgrade_notice' );
	return;
}

/**
 * And only works with PHP 5.6.4 or later.
 */
if ( version_compare( phpversion(), '5.6.4', '<' ) ) {
	/**
	 * Adds a message for outdate PHP version.
	 */
	function awebooking_php_upgrade_notice() {
		$message = sprintf( esc_html__( 'AweBooking requires at least PHP version 5.6.4 to works, you are running version %s. Please contact to your administrator to upgrade PHP version!', 'awebooking' ), phpversion() );
		printf( '<div class="error"><p>%s</p></div>', $message ); // WPCS: XSS OK.

		deactivate_plugins( array( 'awebooking/awebooking.php' ) );
	}

	add_action( 'admin_notices', 'awebooking_php_upgrade_notice' );
	return;
}

/**
 * Load the bootstrap file.
 */
require trailingslashit( __DIR__ ) . 'bootstrap.php';

/* Constants */
define( 'AWEBOOKING_PLUGIN_FILE_PATH', __FILE__ );
define( 'AWEBOOKING_VERSION', AweBooking::VERSION );

/**
 * Let create the AweBooking.
 */
$awebooking = new AweBooking;

register_activation_hook( AWEBOOKING_PLUGIN_FILE_PATH, array( 'AweBooking\Installer', 'install' ) );

$GLOBALS['awebooking'] = $awebooking;
