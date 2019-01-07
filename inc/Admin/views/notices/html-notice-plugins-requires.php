<?php
/**
 * HTML display plugin requires notice.
 *
 * @package AweBooking\Admin\View
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$simple_iconfonts = 'wp-simple-iconfonts/wp-simple-iconfonts.php';

?>

<div class="notice notice-info" style="position: relative;">
	<a class="notice-dismiss" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'abrs-hide-notice', 'plugins_requires' ), 'abrs_hide_notices_nonce', 'notice_nonce' ) ); ?>"><span class="screen-reader-text"><?php esc_html_e( 'Dismiss', 'awebooking' ); ?></span></a>

	<p>
		<?php echo wp_kses_post( __( 'For a better experience, try the <strong>WP Simple Iconfonts</strong> for a powerful new way to integrate icon fonts into your rooms, services and amenities.', 'awebooking' ) ); ?>
	</p>

	<?php if ( file_exists( WP_PLUGIN_DIR . '/' . $simple_iconfonts ) && ! is_plugin_active( $simple_iconfonts ) && current_user_can( 'activate_plugin', $simple_iconfonts ) ) : ?>
		<p>
			<a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'plugins.php?action=activate&plugin=' . $simple_iconfonts . '&plugin_status=active' ), 'activate-plugin_' . $simple_iconfonts ) ); ?>" class="button button-primary"><?php esc_html_e( 'Activate the WP Simple Iconfonts', 'awebooking' ); ?></a>
		</p>
	<?php else : ?>
		<?php
		if ( current_user_can( 'install_plugins' ) ) {
			$url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=wp-simple-iconfonts' ), 'install-plugin_wp-simple-iconfonts' );
		} else {
			$url = 'https://wordpress.org/plugins/wp-simple-iconfonts/';
		}
		?>
		<p>
			<a href="<?php echo esc_url( $url ); ?>" class="button button-primary"><?php esc_html_e( 'Install the WP Simple Iconfonts', 'awebooking' ); ?></a>
		</p>
	<?php endif; ?>
</div>
