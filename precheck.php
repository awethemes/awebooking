<?php
/**
 * The pre-check file.
 *
 * @package AweBooking
 */

if ( ! function_exists( 'awebooking_print_fatal_error' ) ) {
	/**
	 * Print a fatal error, then deactivate AweBooking.
	 *
	 * NOTE: Be careful when call this function, this will/maybe
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
			$message .= '<pre>' . $error . '</pre>';

			// Force the plugin deactivate when catched an exception.
			$deactivate = true;
		} elseif ( is_wp_error( $error ) ) {
			$message = $error->get_error_message();
		} else {
			$message = (string) $error;
		}

		// Print the error message.
		printf( '<div class="error">%s</div>', wp_kses_post( wpautop( $message ) ) );

		// Deactivate the plugin.
		if ( $deactivate ) {
			$plugin_name = function_exists( 'awebooking' ) ? awebooking()->plugin_basename() : 'awebooking/awebooking.php';

			deactivate_plugins( array( $plugin_name ) );
		}
	}
}

if ( ! function_exists( 'awebooking_php_upgrade_notice' ) ) {
	/**
	 * Adds a message for outdate PHP version.
	 *
	 * @throws Exception|Throwable
	 */
	function awebooking_php_upgrade_notice() {
		/* translators: %s Your current PHP version */
		$message = sprintf( esc_html__( 'AweBooking requires at least PHP version 5.6.4 to works, you are running version %s. Please contact to your administrator to upgrade PHP version!', 'awebooking' ), PHP_VERSION );

		awebooking_print_fatal_error( $message, true );
	}
}

if ( ! function_exists( 'awebooking_wordpress_upgrade_notice' ) ) {
	/**
	 * Prints an update nag after an unsuccessful attempt to active
	 * AweBooking on WordPress versions prior to 4.6.
	 *
	 * @global string $wp_version WordPress version.
	 *
	 * @throws Exception|Throwable
	 */
	function awebooking_wordpress_upgrade_notice() {
		/* translators: %s Your current WordPress version */
		$message = sprintf( esc_html__( 'AweBooking requires at least WordPress version 4.6, you are running version %s. Please upgrade and try again!', 'awebooking' ), $GLOBALS['wp_version'] );

		awebooking_print_fatal_error( $message, true );
	}
}
