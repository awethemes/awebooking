<?php

$bulk_controls = abrs_create_form( 'bulk_state_form' );

?>

<div id="bulk-update-dialog" class="awebooking-dialog-contents" title="<?php echo esc_html__( 'Bulk Update State', 'awebooking' ); ?>" style="display: none;">
	<form method="POST" action="<?php echo esc_url( abrs_admin_route( '/calendar/bulk-update' ) ); ?>">
		<?php wp_nonce_field( 'awebooking_bulk_update_state' ); ?>

		<div class="cmb2-wrap awebooking-wrap" style="width: 720px;">
			<div class="cmb2-metabox cmb2-inline-metabox">

				<div class="abrow">
					<div class="abcol-4 abcol-sm-12">
						<div class="cmb-row">
							<div class="cmb-td">
								<label for="bulk_rooms"><?php esc_html_e( 'Select rooms', 'awebooking' ); ?> </label>

								<ul class="list-bulk-rooms">
									<?php foreach ( $scheduler->room_types as $room_type ) : ?>
										<li>
											<strong><?php echo esc_html( $room_type['title'] ); ?></strong>

											<ul>
											<?php foreach ( $room_type->get_rooms() as $room ) : ?>
												<li>
													<input type="checkbox" class="cmb2-option" name="bulk_rooms[]" id="bulk_rooms<?php echo intval( $room['id'] ); ?>" value="<?php echo intval( $room['id'] ); ?>">
													<label for="bulk_rooms<?php echo intval( $room['id'] ); ?>"><?php echo esc_html( $room['name'] ); ?></label>
												</li>
											<?php endforeach ?>
											</ul>
										</li>
									<?php endforeach ?>
								</ul>
							</div>
						</div>
					</div>

					<div class="abcol-8 abcol-sm-12">
						<?php
						$bulk_controls->show_field([
							'id'          => 'bulk_date',
							'type'        => 'abrs_dates',
							'name'        => esc_html__( 'Select dates', 'awebooking' ),
							// 'attributes'  => [ 'tabindex' => '-1' ],
							// 'default'     => [ abrs_date( 'today' )->toDateString(), abrs_date( 'tomorrow' )->toDateString() ],
							'show_js'     => false,
						]);
						?>

						<div class="cmb-row">
							<div class="cmb-th">
								<label for="bulk_state"><?php esc_html_e( 'Select state', 'awebooking' ); ?> </label>
							</div>

							<div class="cmb-td">
								<div class="abrow no-gutters abrs-radio-group">
									<div class="radio-group">
										<input name="bulk_action" type="radio" value="block" class="radio-group__input" id="block" checked="checked">
										<label class="radio-group__label" for="block"><?php esc_html_e( 'Block', 'awebooking' ); ?></label>

										<input name="bulk_action" type="radio" value="unblock" class="radio-group__input" id="unblock">
										<label class="radio-group__label" for="unblock"><?php esc_html_e( 'Unblock', 'awebooking' ); ?></label>
									</div>
								</div>
								<div class="abrs-input-addon group-state">
								</div>
							</div>

						</div>

						<?php
						$bulk_controls->show_field([
							'id'                => 'bulk_days',
							'type'              => 'multicheck_inline',
							'name'              => esc_html__( 'Apply on days', 'awebooking' ),
							'default'           => [ 0, 1, 2, 3, 4, 5, 6 ],
							'attributes'        => [ 'tabindex' => '-1' ],
							'select_all_button' => false,
							'options_cb'        => function() {
								return abrs_days_of_week( 'abbrev' );
							},
						]);
						?>
					</div>
				</div>

			</div><!-- /.cmb2-inline-metabox -->
		</div><!-- /.awebooking-wrap -->

		<div class="awebooking-dialog-buttons">
			<button type="submit" class="button button-primary abrs-button"><?php echo esc_html__( 'Submit', 'awebooking' ); ?></button>
		</div>
	</form>
</div>
