<?php
/* @var $settings, $request */

// Get the current setting, fallback 'general'.
$current_setting = $request->get( 'setting', 'general' );

// Build the tabs array.
$tabs = $settings->all()
	->map( function( $setting ) {
		return $setting->get_label() ?: $setting->get_id();
	})->all();

?><div class="wrap cmb2-options-page awebooking-settings">
	<h1 class="wp-heading-inline screen-reader-text"><?php echo esc_html__( 'Settings', 'awebooking' ); ?></h1>
	<hr class="wp-header-end">

	<form method="POST" enctype="multipart/form-data" action="<?php echo esc_url( abrs_admin_route( '/settings' ) ); ?>">
		<input type="hidden" name="_setting" value="<?php echo esc_attr( $current_setting ); ?>">
		<?php wp_nonce_field( 'awebooking-settings' ); ?>

		<nav class="nav-tab-wrapper abrs-nav-tab-wrapper">

			<?php foreach ( $tabs as $key => $label ) : ?>
				<a class="nav-tab <?php echo ( $key === $current_setting ) ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( abrs_admin_route( '/settings', [ 'setting' => $key ] ) ); ?>"><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>

			<?php do_action( 'awebooking/admin_settings_tabs' ); ?>
		</nav>

		<?php
		if ( $setting = $settings->get( $current_setting ) ) {
			do_action( 'awebooking/before_output_setting', $setting );

			$setting->output( $request );

			do_action( 'awebooking/after_output_setting', $setting );
		}
		?>

		<p class="submit">
			<?php if ( empty( $GLOBALS['hide_save_button'] ) ) : ?>
				<button name="save" class="button-primary" type="submit" value="<?php esc_attr_e( 'Save changes', 'awebooking' ); ?>"><?php esc_html_e( 'Save changes', 'awebooking' ); ?></button>
			<?php endif; ?>
		</p>
	</form>
</div><!-- /.wrap -->