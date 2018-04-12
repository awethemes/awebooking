<div class="wrap">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Calendar', 'awebooking' ); ?></h1>
	<a href="<?php echo esc_url( awebooking( 'url' )->admin_route( 'reservation' ) ); ?>" class="page-title-action"><?php echo esc_html__( 'New Reservation', 'awebooking' ); ?></a>

	<hr class="wp-header-end">

	<div style="padding-top: 1em;"></div>
	<?php $scheduler->display(); ?>
</div>
