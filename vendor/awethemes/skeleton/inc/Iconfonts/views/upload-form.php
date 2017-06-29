<?php
/**
 * Icon manager upload form partial template.
 *
 * @author  Awethemes
 * @package Awecontent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="welcome-panel">
	<a class="welcome-panel-close" href="#upload-icon" data-toggle="collapse" aria-label="<?php esc_html_e( 'Dismiss the upload form', 'skeleton' ); ?>"><?php esc_html_e( 'Dismiss', 'skeleton' ); ?></a>

	<div class="welcome-panel-content">

		<h2><?php echo esc_html__( 'Select Your Files', 'skeleton' ); ?></h2>
		<p><?php echo esc_html__( 'Let’s get started importing your custom icon pack', 'skeleton' ); ?></p>
		<p><?php echo wp_kses_post( __( 'To get started, simply upload a zip file downloaded from <a href="http://fontello.com" target="_blank">Fontello</a> or <a href="https://icomoon.io/app" target="_blank">IcoMoon App</a>.', 'skeleton' ) ); ?></p>

		<form method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->page_slug ) ); ?>" class="wp-upload-form">
			<?php wp_nonce_field( 'skeleton/upload_iconpack' ) ?>

			<p>
				<input type="file" id="icon-zip" name="icon-zip">
				<input type="submit" id="icon-upload" name="icon-upload" class="button" value="<?php esc_html_e( 'Install Now', 'skeleton' ); ?>">
			</p>
		</form>

	</div><!-- /.welcome-panel-content -->
</div><!-- /.welcome-panel -->
