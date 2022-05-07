<?php
/**
 * Plugin Name:     AweBooking
 * Plugin URI:      https://awethemes.com/plugins/awebooking
 * Description:     A simple hotel reservation system for WordPress.
 * Author:          Awethemes
 * Author URI:      https://awethemes.com
 * Text Domain:     awebooking
 * Domain Path:     /languages
 * Version:         3.2.26
 *
 * @package         AweBooking
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

define('AWEBOOKING_PMS_VERSION', '5.0.0-dev');
define('AWEBOOKING_PMS_PLUGIN_FILE', __FILE__);
define('AWEBOOKING_PMS_PLUGIN_DIR_PATH', plugin_dir_path(AWEBOOKING_PMS_PLUGIN_FILE));
define('AWEBOOKING_PMS_PLUGIN_DIR_URL', plugin_dir_url(AWEBOOKING_PMS_PLUGIN_FILE));
define('AWEBOOKING_PMS_ASSETS_PATH', AWEBOOKING_PMS_PLUGIN_DIR_PATH . 'build');
define('AWEBOOKING_PMS_ASSETS_URL', AWEBOOKING_PMS_PLUGIN_DIR_URL . 'build');

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
if ( PHP_VERSION_ID < 50604 ) {
	add_action( 'admin_notices', 'awebooking_php_upgrade_notice' );
	return;
}

// Include the loader.
require_once dirname( __FILE__ ) . '/loader.php';

// Create the AweBooking.
$awebooking = new AweBooking( __FILE__ );

// Load the static config.
$awebooking->load_config( dirname( __FILE__ ) . '/config.php' );

/* @var \AweBooking\Installer $installer */
$installer = $awebooking->make( 'installer' );
$installer->init();

register_activation_hook( __FILE__, array( $installer, 'activation' ) );
register_deactivation_hook( __FILE__, array( $installer, 'deactivation' ) );

// Initialize under 'plugins_loaded'.
add_action( 'plugins_loaded', array( $awebooking, 'initialize' ) );
