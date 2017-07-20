<?php
/**
 * Plugin Name:     AweBooking
 * Plugin URI:      https://awethemes.com/plugins/awebooking
 * Description:     AweBooking is both simple and powerful when it comes to its purpose: booking hotel room. It allows you to setup any reservations quickly, pleasantly and easily.
 * Author:          Awethemes
 * Author URI:      https://awethemes.com
 * Text Domain:     awebooking
 * Domain Path:     /i18n/languages
 * Version:         3.0.0-alpha-305
 *
 * @package         AweBooking
 */

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
	}

	add_action( 'admin_notices', 'awebooking_wordpress_upgrade_notice' );
	return;
}

/**
 * And only works with PHP 5.4.0 or later.
 */
if ( version_compare( phpversion(), '5.4.0', '<' ) ) {
	/**
	 * Adds a message for outdate PHP version.
	 */
	function awebooking_php_upgrade_notice() {
		$message = sprintf( esc_html__( 'AweBooking requires at least PHP version 5.4.0 to works, you are running version %s. Please contact to your administrator to upgrade PHP version!', 'awebooking' ), phpversion() );
		printf( '<div class="error"><p>%s</p></div>', $message ); // WPCS: XSS OK.
	}

	add_action( 'admin_notices', 'awebooking_php_upgrade_notice' );
	return;
}

/**
 * First, we need autoload via Composer to make everything works.
 */
require trailingslashit( __DIR__ ) . '/vendor/autoload.php';

/**
 * Next, load the bootstrap file.
 */
require trailingslashit( __DIR__ ) . '/bootstrap.php';

/**
 * Yeah, now everything okay, we'll create the AweBooking.
 */
$awebooking = new AweBooking;

add_action( 'skeleton/init', array( $awebooking, 'boot' ) );

register_activation_hook( __FILE__, array( 'AweBooking\\Installer', 'create_tables' ) );
// register_deactivation_hook( __FILE__, '' );

$GLOBALS['awebooking'] = $awebooking;
