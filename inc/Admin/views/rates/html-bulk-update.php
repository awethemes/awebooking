<div id="bulk-update-dialog" class="awebooking-dialog-contents" title="<?php echo esc_html__( 'Bulk Update Price', 'awebooking' ); ?>" style="display: none;">
	<form method="POST" action="<?php echo esc_url( abrs_admin_route( '/rates/bulk-update' ) ); ?>">
		<?php wp_nonce_field( 'awebooking_bulk_update_price' ); ?>

		<div class="cmb2-wrap awebooking-wrap" style="width: 720px;">
			<div class="cmb2-metabox cmb2-inline-metabox">

				<div class="abrow abrs-pb1">
					<div class="abcol-4 abcol-sm-12">
						<?php $bulk_controls->show_field( 'bulk_room_types' ); ?>
					</div>

					<div class="abcol-8 abcol-sm-12">
						<?php $bulk_controls->show_field( 'bulk_date' ); ?>

						<div class="cmb-row">
							<div class="cmb-th"><label for="amount"><?php esc_html_e( 'Amount', 'awebooking' ); ?></label></div>

							<div class="cmb-td">
								<div class="abrs-input-addon group-operator-amount">
									<?php $bulk_controls['bulk_operator']->display_control(); ?>
									<?php $bulk_controls['bulk_amount']->display_control(); ?>
								</div>
							</div>
						</div>

						<?php $bulk_controls->show_field( 'bulk_days' ); ?>
					</div>
				</div>

			</div><!-- /.cmb2-inline-metabox -->
		</div><!-- /.awebooking-wrap -->

		<div class="awebooking-dialog-buttons">
			<button type="submit" class="button button-primary abrs-button"><?php echo esc_html__( 'Submit', 'awebooking' ); ?></button>
		</div>
	</form>
</div>
