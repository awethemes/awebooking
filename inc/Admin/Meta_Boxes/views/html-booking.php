<div class="postbox">
	<div class="awebooking-heading clear">
		<a href="#" class="awebooking-featured js-awebooking-toggle-featured">
			<?php if ( $the_booking->is_featured() ) : ?>
				<span class="dashicons dashicons-star-filled"></span>
			<?php else : ?>
				<span class="dashicons dashicons-star-empty"></span>
			<?php endif ?>
		</a>

		<div class="awebooking-heading__toolbox">
			<label>
				<input type="checkbox" name="">
				<span><?php echo esc_html__( 'Checked-in', 'awebooking' ) ?></span>
			</label>

			<label>
				<input type="checkbox" name="">
				<span><?php echo esc_html__( 'Checked-out', 'awebooking' ) ?></span>
			</label>
		</div>

		<?php
		printf( '<h2 class="awebooking-heading__title">%s <span>#%s</span></h2>', esc_html__( 'Booking', 'awebooking' ), $post->ID );
		?>
	</div><!-- /.awebooking-booking-heading -->

	<?php
	$the_booking['transaction_id'] = '113333';
	$the_booking['payment_method_title'] = 'PayPal';

	if ( $the_booking['transaction_id'] ) {
		echo '<br>';

		if ( $the_booking['payment_method_title'] ) {
			echo '<span class="">' . esc_html__( 'Via', 'awebooking' ) . ' ' . esc_html( $the_booking->get_payment_method_title() ) . '</span>';
		}

		echo ' , ';
		echo '<span class="">' . esc_html__( 'Transaction ID:', 'awebooking' ) . ' ' . esc_html( $the_booking->get_transaction_id() ) . '</span>';
	}

	?>

	<div class="clear">
		<div class="booking-column">

		</div>

		<div class="booking-column">
			<p>
				<strong><?php echo esc_html__( 'Check-in:', 'awebooking' ) ?></strong>
				<br>
				<?php echo $the_booking->get_check_in(); ?>
			</p>

			<p>
				<strong><?php echo esc_html__( 'Check-out:', 'awebooking' ) ?></strong>
				<br>
				<?php echo $the_booking->get_check_out(); ?>
			</p>
		</div>

		<div class="booking-column">
			sdfasdasd
		</div>
	</div>

	<div class="clear"></div>

</div><!-- /.postbox -->

<div class="table-responsive">
	<table class="awebooking-table widefat fixed striped">
		<thead>
			<tr>
				<th>Room</th>
				<th>Nights</th>
				<th>Guests</th>
				<th>Price</th>
				<th>#</th>
			</tr>
		</thead>

		<tbody>
			<?php foreach ( $the_booking->get_line_items() as $room_item ) :
				$room_unit = $room_item->get_room_unit(); ?>
				<tr>
					<td>
						<strong><?php echo esc_html( $room_item->get_name() ); ?></strong>

						<?php if ( $room_unit && $room_unit->exists() ) : ?>
							<br>
							(<?php echo esc_html( $room_unit->get_name() ); ?>)
						<?php endif ?>
					</td>

					<td>
						<?php
						try {
							$date_period = $room_item->get_period();
							printf( '<strong>%s</strong> <br> <span>%s - %s</span>',
								$room_item->get_formatted_nights_stayed( false ),
								$date_period->get_start_date(),
								$date_period->get_end_date()
							);
						} catch ( \Exception $e ) {
							echo '<span class="awebooking-invalid">' . esc_html__( 'Period date is invalid', 'awebooking' ) . '</span>';
						}
						?>
					</td>

					<td><?php $room_item->get_fomatted_guest_number(); ?></td>

					<td>
						<?php echo esc_html( $room_item->get_price() ); ?> / night
						<br>
						<strong><?php echo $room_item->get_total(); ?></strong>
					</td>

					<td style="text-align: right;">
						<div>
							<?php
							$service_items = $the_booking->get_service_items()->where( 'parent_id', $room_item->get_id() );

							foreach ( $service_items as $item ) {
								echo $item['name'] . ', ';
							}
							?>
						</div>

						<a href="#" class="button">
							<span class="dashicons dashicons-arrow-down-alt2"></span>
						</a>

						<a href="#" class="js-edit-line-item" data-line-item="<?php echo esc_attr( $room_item->get_id() ); ?>">
							<span class="dashicons dashicons-edit"></span>
							<span class="screen-reader-text"><?php echo esc_html__( 'Edit Room', 'awebooking' ) ?></span>
						</a>

						<a href="<?php echo esc_url( $room_item->get_delete_url() ); ?>" class="js-delete-booking-item">
							<span class="dashicons dashicons-trash"></span>
							<span class="screen-reader-text"><?php echo esc_html__( 'Delete Room', 'awebooking' ) ?></span>
						</a>
					</td>

				</tr>
			<?php endforeach ?>
		</tbody>

		<tfoot>
			<tr>
				<td colspan="4">
					<a href="#awebooking-add-line-item-popup" class="button" data-toggle="awebooking-popup">
						<!-- <span class="dashicons dashicons-plus"></span> -->
						<?php echo esc_html__( 'Add Room Unit', 'awebooking' ); ?>
					</a>
					<a href="#" class="button">Add service</a>
				</td>

				<td colspan="1" style="text-align: right;">
					<strong>Subtotal: <?php echo $the_booking->get_subtotal(); ?></strong>
				</td>
			</tr>
		</tfoot>

	</table>
</div>
