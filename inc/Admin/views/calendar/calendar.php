<?php
/**
 * Template displaying the calendar.
 *
 * @var \AweBooking\Admin\Calendar\Abstract_Scheduler $scheduler
 *
 * @package AweBooking
 */

?>

<div class="wrap">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Calendar', 'awebooking' ); ?></h1>
	<hr class="wp-header-end">

	<?php if ( ! abrs_blank( $scheduler->scheduler ) ) : ?>
		<div class="abrs-toolbar abrs-toolbar--calendar dp-flex">
			<div class="abrs-ptb1">
				<button class="button abrs-button js-open-bulk-update"><?php esc_html_e( 'Bulk Update', 'awebooking' ); ?></button>
			</div>

			<?php $scheduler->call( 'display_main_toolbar' ); ?>
		</div>
	<?php endif; ?>

	<div id="awebooking-avai-scheduler">
		<?php $scheduler->display(); ?>
	</div>
</div>

<?php abrs_admin_template()->partial( 'calendar/html-bulk-update.php', compact( 'scheduler' ) ); ?>

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
