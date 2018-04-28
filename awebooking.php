<?php
/**
 * Plugin Name:     AweBooking
 * Plugin URI:      https://awethemes.com/plugins/awebooking
 * Description:     A simple hotel reservation system for WordPress.
 * Author:          Awethemes
 * Author URI:      https://awethemes.com
 * Text Domain:     awebooking
 * Domain Path:     /languages
 * Version:         3.1.0-dev
 *
 * @package         AweBooking
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// Load the pre-check file.
require_once trailingslashit( dirname( __FILE__ ) ) . 'precheck.php';

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

if ( ! class_exists( 'AweBooking\Plugin', false ) ) {
	// Include the loader.
	require_once dirname( __FILE__ ) . '/loader.php';

	// Create the AweBooking.
	$awebooking = new AweBooking( __FILE__ );

	// Install the awebooking.
	$installer = $awebooking->make( 'installer' );
	register_activation_hook( __FILE__, array( $installer, 'activation' ) );

	// Initialize under 'plugins_loaded'.
	add_action( 'plugins_loaded', array( $awebooking, 'initialize' ) );

	/**
	 * Main instance of AweBooking.
	 *
	 * @param  string|null $make Optional, get special binding in the container.
	 * @return AweBooking\Plugin
	 */
	function awebooking( $make = null ) {
		return is_null( $make )
			? AweBooking::get_instance()
			: AweBooking::get_instance()->make( $make );
	}

	// Deprecated classes & functions.
	require_once dirname( __FILE__ ) . '/deprecated/deprecated.php';
}
