<div class="wrap awebooking-wrap-rates">
	<h1 class="wp-heading-inline"><?php echo esc_html__( 'Pricing', 'awebooking' ); ?></h1>
	<hr class="wp-header-end">

	<div class="abrs-toolbar abrs-search-toolbar dp-flex">
		<div class="abrs-ptb1 pricing-left-actions">
			<button class="button abrs-button js-open-bulk-update"><?php esc_html_e( 'Bulk Adjust Price', 'awebooking' ); ?></button>
		</div>
	</div>

	<div id="awebooking-pricing-scheduler">
		<?php $scheduler->display(); ?>
	</div>
</div><!-- /.wrap -->

<script type="text/javascript">
	var _listRoomTypes = <?php echo $scheduler->room_types ? json_encode( $scheduler->room_types ) : '[]'; ?>;
</script>

<?php $this->partial( 'rates/html-adjust-price.php' ); ?>

<?php $this->partial( 'rates/html-bulk-update.php', compact( 'scheduler' ) ); ?>
