<?php

use AweBooking\Admin\Forms\Booking_General_From;

?><div class="postbox">
	<div class="awebooking-heading clear">
		<div class="awebooking-featured js-awebooking-toggle-featured">
			<input type="checkbox" name="booking_featured" id="booking-featured-checkbox" <?php echo ($the_booking->is_featured()) ? 'checked=""' : ''; ?>>
			<label for="booking-featured-checkbox"></label>
		</div>

		<div class="awebooking-heading__toolbox">
			<label>
				<input type="checkbox" name="booking_checked_in" value="1" <?php echo ($the_booking->is_checked_in()) ? 'checked=""' : ''; ?>>
				<span><?php esc_html_e( 'Checked-in', 'awebooking' ); ?></span>
			</label>

			<label>
				<input type="checkbox" name="booking_checked_out" value="1" <?php echo ($the_booking->is_checked_out()) ? 'checked=""' : ''; ?>>
				<span><?php esc_html_e( 'Checked-out', 'awebooking' ); ?></span>
			</label>
		</div>

		<?php printf( '<h2 class="awebooking-heading__title">%s <span>#%s</span></h2>', esc_html__( 'Booking', 'awebooking' ), absint( $post->ID ) ); ?>
	</div><!-- /.awebooking-booking-heading -->

	<?php
	// $the_booking['transaction_id'] = '113333';
	// $the_booking['payment_method_title'] = 'PayPal';

	if ( $the_booking['transaction_id'] ) {
		echo '<br>';

		if ( $the_booking['payment_method_title'] ) {
			echo '<span class="">' . esc_html__( 'Via', 'awebooking' ) . ' ' . esc_html( $the_booking->get_payment_method_title() ) . '</span>';
		}

		echo ' , ';
		echo '<span class="">' . esc_html__( 'Transaction ID:', 'awebooking' ) . ' ' . esc_html( $the_booking->get_transaction_id() ) . '</span>';
	}
	?>

	<div class="booking-wrapper">
		<div class="clear booking-row">

			<div class="booking-column">
				<div class="awebooking-block-form">
					<?php (new Booking_General_From( $the_booking ))->output(); ?>
				</div>
			</div>

			<div class="booking-column info-column">

				<?php if ( ! $the_booking->is_multiple_rooms() ) : ?>
					<p>
						<?php if ( $the_booking->get_arrival_date() ) : ?>
							<strong><?php esc_html_e( 'Check-in:', 'awebooking' ); ?></strong>
							<?php echo esc_html( $the_booking->get_arrival_date()->to_wp_date_string() ); ?>
						<?php endif; ?>

						<?php if ( $the_booking->get_departure_date() ) : ?>
							<strong><?php esc_html_e( 'Check-out:', 'awebooking' ); ?></strong>
							<?php echo esc_html( $the_booking->get_departure_date()->to_wp_date_string() ); ?>
						<?php endif; ?>
					</p>
				<?php else : ?>
					<?php if ( $the_booking->get_arrival_date() ) : ?>
						<p>
							<strong><?php esc_html_e( 'Arrival:', 'awebooking' ); ?></strong>
							<?php echo esc_html( $the_booking->get_arrival_date()->to_wp_date_string() ); ?>
						</p>
					<?php endif; ?>

					<?php if ( $the_booking->get_departure_date() ) : ?>
						<p>
							<strong><?php esc_html_e( 'Departure:', 'awebooking' ); ?></strong>
							<?php echo esc_html( $the_booking->get_departure_date()->to_wp_date_string() ); ?>
						</p>
					<?php endif; ?>
					
					<?php if ( ! $the_booking->is_continuous_periods() ) : ?>
						<p class="awebooking-label awebooking-label--warning"><?php esc_html_e( 'Interrupted reservation. Please check the booking detail below.', 'awebooking' ); ?></p>
					<?php endif ?>
				<?php endif ?>
				
				<p>
					<strong><?php esc_html_e( 'Night(s):', 'awebooking' ); ?></strong>
					<?php echo esc_html( $the_booking->calculate_nights_stayed() ); ?>
				</p>

				<p>
					<strong><?php esc_html_e( 'Guest(s):', 'awebooking' ); ?></strong>
					<?php $the_booking->get_fomatted_guest_number(); ?>
				</p>
			</div>
			
			<?php if ( $the_booking->get_customer_note() ) : ?>
				<div class="booking-column note-column">
					<strong><?php esc_html_e( 'Note:', 'awebooking' ); ?>&nbsp;</strong>
					<div class="note_content">
						<p>
							<?php echo esc_html( $the_booking->get_customer_note() ); ?>
						</p>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<div class="clear"></div>
</div><!-- /.postbox -->

<div class="table-responsive">
	<?php foreach ( $the_booking->get_line_items() as $room_item ) :
		$room_unit = $room_item->get_room_unit();
		$service_items = $the_booking->get_service_items()->where( 'parent_id', $room_item->get_id() );
		?>

		<table class="awebooking-table widefat fixed" style="margin-bottom: 5px;">
			<thead>
				<tr>
					<td>
						<strong><?php echo esc_html( $room_item->get_name() ); ?></strong>

						<?php if ( $room_unit && $room_unit->exists() ) : ?>
							(<?php echo esc_html( $room_unit->get_name() ); ?>)
						<?php endif ?>
					</td>

					<td>
						<?php
						try {
							$date_period = $room_item->get_period();
							printf( '<strong>%s</strong> (<span>%s - %s</span>)',
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
						<?php echo esc_html( $room_item->get_total() ); ?>
					</td>

					<td style="text-align: right;">
						<div style="position: relative;">
							<a href="#" class="button awebooking-button-dashicons" data-init="awebooking-toggle">
								<span class="dashicons dashicons-arrow-down-alt2"></span>
							</a>

							<ul class="split-button-body awebooking-main-toggle">
								<li>
									<a href="#" class="js-edit-line-item" data-line-item="<?php echo esc_attr( $room_item->get_id() ); ?>">
										<span><?php esc_html_e( 'Edit Room', 'awebooking' ); ?></span>
										<span class="dashicons dashicons-edit"></span>
									</a>
								</li>

								<li>
									<a href="<?php echo esc_url( $room_item->get_delete_url() ); ?>" class="js-delete-booking-item">
										<span><?php esc_html_e( 'Delete Room', 'awebooking' ); ?></span>
										<span class="dashicons dashicons-trash"></span>
									</a>
								</li>
							</ul>
						</div>
					</td>

				</tr>
			</thead>

			<tbody>

				<?php foreach ( $service_items as $service_item ) : ?>

					<tr>
						<td>
							<?php echo $service_item->get_name(); ?>
						</td>
					</tr>

				<?php endforeach ?>

			</tbody>
		</table>
	<?php endforeach ?>
</div>

<div>
	<a href="#awebooking-add-line-item-popup" class="button" data-toggle="awebooking-popup">
		<?php esc_html_e( 'Add Room Unit', 'awebooking' ); ?>
	</a>

	<strong><?php printf( esc_html__( 'Subtotal: %s' ), $the_booking->get_subtotal() ); ?></strong>
</div>
