<style type="text/css">
	table.awebooking-striped-table {
		border-left: none;
		border-right: none;
	}
</style>

<div class="postbox">
	<div class="">

	<?php
	printf( '<h1 class="wp-heading-inline awebooking-title">%s <span>#%s</span></h1>', esc_html__( 'Booking', 'awebooking' ), $post->ID );

	if ( $the_booking['transaction_id'] ) {
		echo '<br>';

		if ( $the_booking['payment_method_title'] ) {
			echo '<span class="">' . esc_html__( 'Via', 'awebooking' ) . ' ' . esc_html( $the_booking->get_payment_method_title() ) . '</span>';
		}

		echo ' | ';

		echo '<span class="">' . esc_html__( 'Transaction ID:', 'awebooking' ) . ' ' . esc_html( $the_booking->get_transaction_id() ) . '</span>';
	}
	?>

	<div class="table-responsive">
		<table class="awebooking-striped-table widefat fixed striped">
			<thead>
				<tr>
					<th>Room</th>
					<th>Nights</th>
					<th>Guests</th>
					<th>Price</th>
					<th>Charge / stay</th>
					<th></th>
				</tr>
			</thead>

			<tbody>
			<?php foreach ( $the_booking->get_room_items() as $room_item ) :
				$room_unit = $room_item->get_booking_room(); ?>
				<tr>
					<td>
						<?php echo esc_html( $room_item->get_name() ); ?>
						<br>
						(<?php echo esc_html( $room_unit->get_name() ); ?>)
					</td>

					<td>
						<?php
						try {
							$date_period = $room_item->get_date_period();

							printf( '<strong>%s %s</strong> <br> <span>%s</span> - <span>%s</span>',
								$date_period->nights(),
								_n( 'night', 'nights', $date_period->nights(), 'awebooking' ),
								$date_period->get_start_date()->toDateString(),
								$date_period->get_end_date()->toDateString()
							);
						} catch ( \Exception $e ) {
							echo '<span class="awebooking-invalid">' . esc_html__( 'Period date is invalid', 'awebooking' ) . '</span>';
						}
					?>
					</td>

					<td>
					<?php
					printf( '<span class="">%1$d %2$s</span>',
						$room_item->get_adults(),
						_n( 'adult', 'adults', $room_item->get_adults(), 'awebooking' )
					);

					if ( $room_item->get_children() ) {
						printf( ' &amp; <span class="">%1$d %2$s</span>',
							$room_item->get_children(),
							_n( 'child', 'children', $room_item->get_children(), 'awebooking' )
						);
					}
					?>
					</td>

					<td>
						<?php echo esc_html( $room_item->get_total() ); ?> / night
					</td>

					<td>
						<strong><?php echo $room_item->get_total_price(); ?></strong>
					</td>

					<td style="text-align: right;">
						<a href="<?php echo esc_url( $room_item->get_edit_url() ); ?>">
							<span class="dashicons dashicons-edit"></span>
							<span class="screen-reader-text"><?php echo esc_html__( 'Edit Room', 'awebooking' ) ?></span>
						</a>

						<a href="<?php echo esc_url( $room_item->get_delete_url() ); ?>">
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
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=awebooking-add-item&booking=' . $the_booking->get_id() ) ); ?>" class="button"><?php echo esc_html__( 'Add accommodation', 'awebooking' ); ?></a>
						<a href="#" class="button">Add service</a>
					</td>

					<td colspan="2">
						<strong>Total charge: <?php echo $the_booking->get_subtotal(); ?></strong>
					</td>
				</tr>
			</tfoot>

		</table>
	</div>

	</div>
</div>
