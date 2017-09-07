<?php
/**
 * Plugin Name:     AweBooking
 * Plugin URI:      https://awethemes.com/plugins/awebooking
 * Description:     AweBooking is both simple and powerful when it comes to its purpose: booking hotel room. It allows you to setup any reservations quickly, pleasantly and easily.
 * Author:          Awethemes
 * Author URI:      https://awethemes.com
 * Text Domain:     awebooking
 * Domain Path:     /languages
 * Version:         3.0.0-beta5
 *
 * @package         AweBooking
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * AweBooking only works in WordPress 4.6 or later.
 */
if ( version_compare( $GLOBALS['wp_version'], '4.6', '<' ) ) {
	add_action( 'admin_notices', 'awebooking_wordpress_upgrade_notice' );
	return;
}

/**
 * And only works with PHP 5.6.4 or later.
 */
if ( version_compare( phpversion(), '5.6.4', '<' ) ) {
	add_action( 'admin_notices', 'awebooking_php_upgrade_notice' );
	return;
}

/**
 * Load the bootstrap file.
 */
require trailingslashit( __DIR__ ) . 'bootstrap.php';

/* Constants */
define( 'AWEBOOKING_VERSION', AweBooking::VERSION );
define( 'AWEBOOKING_PLUGIN_FILE_PATH', __FILE__ );

/**
 * Let create the AweBooking.
 */
$awebooking = new AweBooking;

add_action( 'skeleton/init', array( $awebooking, 'boot' ) );
register_activation_hook( __FILE__, array( 'AweBooking\\Installer', 'install' ) );

$GLOBALS['awebooking'] = $awebooking;
