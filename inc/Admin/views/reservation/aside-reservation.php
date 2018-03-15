	<table class="wp-list-table widefat fixed">
		<thead>
			<tr>
				<th>Rooms</th>
			</tr>
		</thead>

		<tbody>
			<?php if ( isset( $reservation ) && $reservation->get_rooms()->isNotEmpty() ) : ?>

				<?php $index = 1; ?>
				<?php foreach ( $reservation->get_rooms() as $room_id => $room_item ) : ?>

					<tr>
						<td>
							<div class="awebooking-reservation__room">
								<span class="awebooking-reservation__room-counter"><?php echo esc_html( $index ); ?></span>

								<a href="#" class="button awebooking-button-dashicons">
									<span class="screen-reader-text"><?php esc_html_e( 'Remove selected', 'awebooking' ); ?></span>
									<span class="dashicons dashicons-no-alt"></span>
								</a>

								<p><strong><?php echo esc_html( $room_item->get_label() ); ?></strong></p>
								<p><strong><?php esc_html_e( 'Guest:', 'awebooking' ); ?></strong> <?php echo wp_kses_post( $room_item->get_guest() ); ?></p>
								<p style="margin-bottom: 0;"><strong><?php esc_html_e( 'Total:', 'awebooking' ); ?> <?php echo esc_html( $room_item->get_pricing()->get_amount() ); ?></strong></p>

								<a href="#breakdown-table-<?php echo esc_attr( $room_id ); ?>" data-toggle="awebooking-popup">Breakdown</a>
								<div id="breakdown-table-<?php echo esc_attr( $room_id ); ?>" title="Breakdown pricing" class="awebooking-dialog-contents hidden">
									<?php $breakdown = $room_item->get_pricing()->get_breakdown(); ?>
									<div class="" style="height: 500px; overflow: auto;">
									<table class="awebooking-debug-rooms__table">
										<thead>
											<tr>
												<th>Night</th>
												<th>Price</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ( $breakdown as $night ) : ?>
												<tr>
													<td><?php echo $night->get_start_date()->to_date_string(); ?></td>
													<td><?php echo $night->get_amount(); ?></td>
												</tr>
											<?php endforeach ?>
										</tbody>
									</table>
									</div>
								</div>

							</div>
						</td>
					</tr>

					<?php $index++; ?>
				<?php endforeach ?>

			<?php else : ?>
				<tr>
					<td colspan="1">Add rooms to start your reservation</td>
				</tr>
			<?php endif ?>
		</tbody>
	</table>

	<div class="clear" style="margin-bottom: 1.5em;"></div>

	<?php $totals = $reservation->get_totals(); ?>

	<table class="wp-list-table widefat fixed">
		<thead>
			<tr>
				<th colspan="2"><?php echo esc_html__( 'Summary', 'awebooking' ); ?></th>
			</tr>
		</thead>

		<tbody>
			<tr>
				<td><?php echo esc_html__( 'Sub-Total', 'awebooking' ); ?></td>
				<td><?php echo $totals->get_subtotal(); ?></td>
			</tr>

			<tr>
				<td><?php echo esc_html__( 'Grand-Total', 'awebooking' ); ?></td>
				<td>10</td>
			</tr>

			<tr>
				<td><?php echo esc_html__( 'Suggested Deposit', 'awebooking' ); ?></td>
				<td>200</td>
			</tr>

			<tr>
				<td><?php echo esc_html__( 'Balance Due', 'awebooking' ); ?></td>
				<td>200</td>
			</tr>

			<tr>
				<td colspan="2" style="text-align: right;">
					<button class="button">Next</button>
				</td>
			</tr>
		</tbody>
	</table>
