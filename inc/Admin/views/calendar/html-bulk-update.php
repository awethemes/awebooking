<?php

use AweBooking\Support\Carbonate;

$bulk_controls = abrs_create_form( 'bulk_state_form' );

?>

<div id="bulk-update-dialog" class="awebooking-dialog-contents" title="<?php echo esc_html__( 'Bulk Update State', 'awebooking' ); ?>" style="display: none;">
	<form method="POST" action="<?php echo esc_url( abrs_admin_route( '/calendar/bulk-update' ) ); ?>">
		<?php wp_nonce_field( 'awebooking_bulk_update_calendar' ); ?>

		<div class="cmb2-wrap awebooking-wrap" style="width: 720px;">
			<div class="cmb2-metabox cmb2-inline-metabox">

				<div class="abrow abrs-pb1">
					<div class="abcol-4 abcol-sm-12">
						<div class="cmb-row cmb-type-multicheck cmb2-id-bulk-room-types" data-fieldtype="multicheck">
							<div class="cmb-th">
								<label for="bulk_room_types"><?php esc_html_e( 'Select room(s)', 'awebooking' ); ?> </label>
							</div>

							<div class="cmb-td">
								<ul class="cmb2-checkbox-list cmb2-list">
									<?php foreach ( $scheduler->room_types as $room_type ) : ?>
										<li class="group-label abrs-pt1"><strong><?php echo esc_html( $room_type['title'] ); ?></strong></li>
										<?php foreach ( $room_type->get_rooms() as $room ) : ?>
											<li>
												<input type="checkbox" class="cmb2-option" name="bulk_rooms[]" id="bulk_rooms<?php echo intval( $room['id'] ); ?>" value="<?php echo intval( $room['id'] ); ?>">
												<label for="bulk_rooms<?php echo intval( $room['id'] ); ?>"><?php echo esc_html( $room['name'] ); ?></label>
											</li>
										<?php endforeach ?>
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
							'show_js'     => false,
							'default'     => [ Carbonate::today()->format( 'Y-m-d' ), Carbonate::tomorrow()->format( 'Y-m-d' ) ],
							'attributes'  => [ 'tabindex' => '-1' ],
						]);
						?>

						<div class="cmb-row">

							<div class="cmb-td">
								<div class="abrow no-gutters abrs-radio-group">


									<div class="radio-group">
										<input name="git_repository" type="radio" value="1" class="radio-group__input" id="git-repository" checked="">
										<label class="radio-group__label" for="git-repository">Git repository</label>
										<input name="git_repository" type="radio" value="0" class="radio-group__input" id="manual-repository">
										<label class="radio-group__label" for="manual-repository">Manual repository</label>
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
								return abrs_week_days( 'abbrev' );
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
