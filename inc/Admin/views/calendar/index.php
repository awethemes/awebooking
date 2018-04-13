<div class="wrap">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Calendar', 'awebooking' ); ?></h1>
	<a href="<?php echo esc_url( awebooking( 'url' )->admin_route( 'reservation' ) ); ?>" class="page-title-action"><?php echo esc_html__( 'New Reservation', 'awebooking' ); ?></a>
	<hr class="wp-header-end">

	<div class="abrs-toolbar"></div>

	<div id="awebooking-avai-scheduler">
		<?php $scheduler->display(); ?>
	</div>
</div>

<form method="POST" action="<?php echo esc_url( abrs_admin_route( '/calendar' ) ); ?>" style="display: none;">
	<?php wp_nonce_field( 'awebooking_update_state' ); ?>
	<div id="js-scheduler-form-controls"></div>
</form>

<script type="text/template" id="tmpl-scheduler-pricing-controls">
	<input type="hidden" name="action" value="{{ data.action }}">
	<input type="hidden" name="room" value="{{ data.calendar }}">
	<input type="hidden" name="end_date" value="{{ data.endDate }}">
	<input type="hidden" name="start_date" value="{{ data.startDate }}">
</script>
