<div class="wrap awebooking-wrap-rates">
	<h1 class="wp-heading-inline"><?php echo esc_html__( 'Pricing', 'awebooking' ); ?></h1>
	<hr class="wp-header-end">

	<div class="abrs-toolbar">
		sdasd
	</div>

	<div id="awebooking-pricing-scheduler">
		<?php $scheduler->display(); ?>
	</div>
</div><!-- /.wrap -->

<script type="text/javascript">
	var _listRoomTypes = <?php echo $scheduler->room_types ? json_encode( $scheduler->room_types ) : '[]'; ?>;
</script>

<div id="scheduler-form-dialog" class="awebooking-dialog-contents" title="<?php echo esc_html__( 'Adjust Price', 'awebooking' ); ?>" style="display: none;">
	<form method="POST" action="<?php echo esc_url( abrs_admin_route( '/rates' ) ); ?>">
		<?php wp_nonce_field( 'awebooking_update_price' ); ?>

		<h2 class="screen-reader-text"><?php echo esc_html__( 'Set room price', 'awebooking' ); ?></h2>

		<div id="js-scheduler-form-controls"></div>

		<div class="awebooking-dialog-buttons">
			<button type="submit" class="button button-primary abrs-button"><?php echo esc_html__( 'Submit', 'awebooking' ); ?></button>
		</div>
	</form>
</div>

<script type="text/template" id="tmpl-scheduler-pricing-controls">
	<input type="hidden" name="action" value="{{ data.action }}">
	<input type="hidden" name="calendar" value="{{ data.calendar }}">

	<# if (data.roomtype) { #>
		<h3 class="abrs-mt0" style="margin-bottom: 10px;">{{ data.roomtype.title }}</h3>
	<# } #>

	<div class="cmb2-wrap awebooking-wrap">
		<div class="cmb2-inline-metabox">
			<?php $controls->show_field( 'date' ); ?>

			<div class="cmb-row">
				<div class="cmb-th"><label for="amount"><?php esc_html_e( 'Amount', 'awebooking' ); ?></label></div>

				<div class="cmb-td">
					<div class="abrs-input-addon group-operator-amount">
						<?php $controls['operator']->display_control(); ?>
						<?php $controls['amount']->display_control(); ?>
					</div>
				</div>
			</div>

			<?php $controls->show_field( 'days' ); ?>

		</div><!-- /.cmb2-inline-metabox -->
	</div><!-- /.awebooking-wrap -->
</script>
