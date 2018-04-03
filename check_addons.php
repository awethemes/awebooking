<?php
if ( version_compare( AweBooking::VERSION, '3.1', '<' ) ) {

	/**
	 * Add major update notice.
	 */
	function awebooking_add_major_update_notice() {
		?>
		<div class="awebooking_plugin_upgrade_notice"><?php esc_html_e( '3.1 is a major update. Make a full site backup, update your theme and extensions before upgrading.', 'awebooking' ); ?></div>
		<?php
	}
	add_action( 'in_plugin_update_message-awebooking/awebooking.php', 'awebooking_add_major_update_notice', 10, 2 );

	/**
	 * Confirmation update plugin.
	 */
	function awebooking_plugin_screen_confirmation_js() {
		?>
		<script>
			( function( $ ) {

				// Trigger the update if the user accepts the confirmation's warning.
				$( '#awebooking-update a.update-link' ).on( 'click', function( evt ) {
					return confirm('Are you sure?');
				});
			})( jQuery );
		</script>
		<?php
	}
	add_action( 'admin_print_footer_scripts', 'awebooking_plugin_screen_confirmation_js' );
} else {

	/**
	 * Check addons.
	 *
	 * @return void
	 */
	function awebooking_check_addons() {
		$addons = awebooking_get_addons();

		foreach ( $addons as $plugin_basename => $plugin ) {
			if ( version_compare( $plugin['Version'], '1.0', '<' ) ) {

				if ( is_plugin_active( $plugin_basename ) ) {
					add_action( 'admin_notices', function() use ( $plugin ) {
						if ( 'admin_notices' !== current_action() ) {
							return;
						}

						$message = sprintf( esc_html__( 'Sorry, the current version of AweBooking is not compatible with %s.', 'awebooking' ), $plugin['Name'] );
						printf( '<div class="error">%s</div>', wp_kses_post( wpautop( $message ) ) );
					} );

					deactivate_plugins( array( plugin_basename( $plugin_basename ) ) );
				}
			}
		}
	}
	add_action( 'plugins_loaded', 'awebooking_check_addons' );

	/**
	 * Get plugins which "maybe" are for AweBooking.
	 *
	 * @return array of plugin info arrays
	 */
	function awebooking_get_addons() {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$plugins = get_plugins();
		$matches = [];

		foreach ( $plugins as $file => $plugin ) {
			if ( $plugin['Name'] !== 'AweBooking' && ( stristr( $plugin['Name'], 'awebooking' ) ) ) {
				$matches[ $file ] = $plugin;
			}
		}

		return apply_filters( 'awebooking/get_addons', $matches, $plugins );
	}
}
