
<h1><?php esc_html_e( 'Add new a tax or fee', 'awebooking' ); ?></h1>
<div class="wrap">
	<form method="POST" action="<?php echo esc_url( awebooking( 'url' )->admin_route( "tax/store" ) ); ?>">
		<?php wp_nonce_field( 'create_tax', '_wpnonce', true ); ?>

		<?php $controls->output(); ?>

		<button class="button button-primary" type="submit"><?php esc_html_e( 'Add New', 'awebooking' ); ?></button>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=awebooking-settings&tab=reservation&section=reservation_tax' ) ); ?>" class="button"><?php esc_html_e( 'Cancel', 'awebooking' ); ?></a>
	</form>
</div>
