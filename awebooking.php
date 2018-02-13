<?php
/**
 * Plugin Name:     AweBooking
 * Plugin URI:      https://awethemes.com/plugins/awebooking
 * Description:     AweBooking is both simple and powerful when it comes to its purpose: booking hotel room. It allows you to setup any reservations quickly, pleasantly and easily.
 * Author:          Awethemes
 * Author URI:      https://awethemes.com
 * Text Domain:     awebooking
 * Domain Path:     /languages
 * Version:         3.0.5
 *
 * @package         AweBooking
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! function_exists( 'awebooking_print_fatal_error' ) ) {
	/**
	 * Print a fatal error, then deactivate AweBooking.
	 *
	 * NOTE: Careful when call this function, this will/maybe
	 * deactivate current running AweBooking without any confirms.
	 *
	 * @param  mixed   $error      The error message, WP_Error, Exception or Throwable.
	 * @param  boolean $deactivate Deactivate AweBooking after that?.
	 * @return void
	 *
	 * @throws Exception|Throwable
	 */
	function awebooking_print_fatal_error( $error, $deactivate = false ) {
		if ( 'admin_notices' !== current_action() ) {
			_doing_it_wrong( __FUNCTION__, 'This function work only in `admin_notices` action.', '3.0.0' );
			return;
		}

		if ( $error instanceof Throwable || $error instanceof Exception ) {
			// When current site in DEBUG mode, just throw that exception.
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				throw $error;
			}

			$message  = esc_html__( 'Sorry, a fatal error occurred. AweBooking will be deactivate to ensure your website is safe.', 'awebooking' );
			$message .= '<pre>' . (string) $error . '</pre>';

			// Force the plugin deactivate when catched an exception.
			$deactivate = true;
		} elseif ( is_wp_error( $error ) ) {
			$message = $error->get_error_message();
		} else {
			$message = (string) $error;
		}

		// Print the error message.
		printf( '<div class="error">%s</div>', wp_kses_post( wpautop( $message ) ) );

		if ( $deactivate ) {
			$plugin_name = isset( $GLOBALS['awebooking'] ) ? $GLOBALS['awebooking']->plugin_basename() : 'awebooking/awebooking.php';

			deactivate_plugins( array( $plugin_name ) );
		}
	}
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
		/* translators: %s Your current WordPress version */
		$message = sprintf( esc_html__( 'AweBooking requires at least WordPress version 4.6, you are running version %s. Please upgrade and try again!', 'awebooking' ), $GLOBALS['wp_version'] );

		awebooking_print_fatal_error( $message, true );
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
	 *
	 * @return void
	 */
	function awebooking_php_upgrade_notice() {
		/* translators: %s Your current PHP version */
		$message = sprintf( esc_html__( 'AweBooking requires at least PHP version 5.6.4 to works, you are running version %s. Please contact to your administrator to upgrade PHP version!', 'awebooking' ), phpversion() );

		awebooking_print_fatal_error( $message, true );
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

$awebooking = new AweBooking( __FILE__ );

try {
	$awebooking->initialize();
} catch ( \Exception $e ) {
	$awebooking->catch_exception( $e );
}

$GLOBALS['awebooking'] = $awebooking;
