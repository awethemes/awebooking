<?php
/**
 * Template displaying the pricing calendar.
 *
 * @var \AweBooking\Admin\Calendar\Abstract_Scheduler $scheduler
 *
 * @package AweBooking
 */

?>
<div class="wrap awebooking-wrap-rates">
	<h1 class="wp-heading-inline"><?php echo esc_html__( 'Pricing', 'awebooking' ); ?></h1>
	<hr class="wp-header-end">

	<?php if ( ! abrs_blank( $scheduler->scheduler ) ) : ?>
		<div class="abrs-toolbar abrs-toolbar--calendar dp-flex">
			<div class="abrs-ptb1">
				<button class="button abrs-button js-open-bulk-update"><?php esc_html_e( 'Bulk Adjust Price', 'awebooking' ); ?></button>
			</div>

			<?php $scheduler->call( 'display_main_toolbar' ); ?>
		</div>
	<?php endif; ?>

	<div id="awebooking-pricing-scheduler">
		<?php $scheduler->display(); ?>
	</div>
</div><!-- /.wrap -->

<script type="text/javascript">
	var _listRoomTypes = <?php echo $scheduler->room_types ? json_encode( $scheduler->room_types ) : '[]'; ?>;
</script>

<?php abrs_admin_template()->partial( 'rates/html-adjust-price.php' ); ?>
<?php abrs_admin_template()->partial( 'rates/html-bulk-update.php', compact( 'scheduler' ) ); ?>
