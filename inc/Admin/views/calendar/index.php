<div class="wrap">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Calendar', 'awebooking' ); ?></h1>
	<a href="<?php echo esc_url( awebooking( 'url' )->admin_route( 'reservation' ) ); ?>" class="page-title-action"><?php echo esc_html__( 'New Reservation', 'awebooking' ); ?></a>
	<hr class="wp-header-end">

	<div class="abrs-toolbar">
		sdasd
	</div>

	<div id="awebooking-avai-scheduler">
		<?php $scheduler->display(); ?>
	</div>
</div>

<div id="scheduler-form-dialog" class="awebooking-dialog-contents" title="<?php echo esc_html__( 'Update State', 'awebooking' ); ?>" style="display: none;">
	<form method="POST" action="<?php echo esc_url( abrs_admin_route( '/calendar' ) ); ?>">
		<?php wp_nonce_field( 'awebooking_update_state' ); ?>

		<div id="js-scheduler-form-controls"></div>

		<div class="awebooking-dialog-buttons">
			<button type="submit" class="button button-primary abrs-button"><?php echo esc_html__( 'Apply', 'awebooking' ); ?></button>
		</div>
	</form>
</div>

<script type="text/template" id="tmpl-scheduler-pricing-controls">
	<input type="hidden" name="action" value="{{ data.action }}">
	<input type="hidden" name="calendar" value="{{ data.calendar }}">

	<input type="hidden" name="end_date" value="{{ data.endDate }}">
	<input type="hidden" name="start_date" value="{{ data.startDate }}">

	<# if (data.roomtype) { #>
		<h3 class="abrs-mt0" style="margin-bottom: 10px;">{{ data.roomtype.title }}</h3>
	<# } #>

	<div class="cmb2-wrap awebooking-wrap">
		<div class="cmb2-inline-metabox">
			<?php // $controls->show_field( 'date' ); ?>
			<?php // $controls->show_field( 'days' ); ?>
		</div><!-- /.cmb2-inline-metabox -->
	</div><!-- /.awebooking-wrap -->
</script>
