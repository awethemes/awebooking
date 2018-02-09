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
					<?php
					do_action( 'awebooking/booking/html_before_general_form', $the_booking );

					( new Booking_General_From( $the_booking ) )->output();

					do_action( 'awebooking/booking/html_after_general_form', $the_booking );
					?>
				</div>
			</div>

			<div class="booking-column info-column">

				<?php if ( ! $the_booking->is_multiple_rooms() ) : ?>
					<p>
						<?php if ( $the_booking->get_arrival_date() ) : ?>
							<strong><?php esc_html_e( 'Check-in:', 'awebooking' ); ?></strong>
							<?php echo esc_html( $the_booking->get_arrival_date()->to_wp_date_string() ); ?>
						<?php endif; ?>
					</p>

					<p>
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
				<?php endif ?>

				<p>
					<strong><?php esc_html_e( 'Night(s):', 'awebooking' ); ?></strong>
					<?php echo esc_html( $the_booking->calculate_nights_stayed() ); ?>
				</p>

				<p>
					<strong><?php esc_html_e( 'Guest(s):', 'awebooking' ); ?></strong>
				</p>
			</div>

			<?php if ( $the_booking->get_customer_note() ) : ?>
				<div class="booking-column note-column">
					<strong><?php esc_html_e( 'Note:', 'awebooking' ); ?>&nbsp;</strong>

					<div class="note_content">
						<?php echo wp_kses_post( wpautop( $the_booking->get_customer_note() ) ); ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<div class="clear"></div>
</div><!-- /.postbox -->
Length of stay varies, see each unit
