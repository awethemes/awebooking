
<div class="wrap">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Edit the tax or fee', 'awebooking' ); ?></h1>
	<hr class="wp-header-end">
	<form method="POST" action="<?php echo esc_url( awebooking( 'url' )->admin_route( "tax/{$tax->id}" ) ); ?>">
		<?php wp_nonce_field( 'update_tax', '_wpnonce', true ); ?>

		<?php $controls->output(); ?>

		<input type="hidden" name="_method" value="PUT">
		<button class="button button-primary" type="submit"><?php esc_html_e( 'Update', 'awebooking' ); ?></button>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=awebooking-settings&tab=reservation&section=reservation_tax' ) ); ?>" class="button"><?php esc_html_e( 'Cancel', 'awebooking' ); ?></a>
	</form>
</div>
