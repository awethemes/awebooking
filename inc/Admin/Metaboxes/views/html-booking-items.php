<style type="text/css">
	#awebooking-booking-rooms .hndle,
	#awebooking-booking-rooms .handlediv { display: none;}
	#awebooking-booking-data.closed .inside { display: block !important; }
</style>

<div class="booking-sections">
	<div class="booking-section booking-section--rooms">
		<?php include trailingslashit( __DIR__ ) . 'html-booking-rooms.php'; ?>
	</div>

	<div class="booking-section booking-section--services">
		<?php include trailingslashit( __DIR__ ) . 'html-booking-services.php'; ?>
	</div>

	<div class="booking-section booking-section--fees">
		<?php include trailingslashit( __DIR__ ) . 'html-booking-fees.php'; ?>
	</div>

	<div class="booking-section booking-section--totals abrs-clearfix">
		<table class="awebooking-table abrs-booking-totals">
			<tbody>
				<?php do_action( 'abrs_before_booking_total', $the_booking ); ?>

				<tr>
					<th><?php echo esc_html__( 'Total:', 'awebooking' ); ?></th>
					<td><?php abrs_price( $the_booking->get( 'total' ), $the_booking->get( 'currency' ) ); ?></td>
				</tr>

				<tr>
					<th><?php echo esc_html__( 'Paid:', 'awebooking' ); ?></th>
					<td><?php abrs_price( $the_booking->get( 'paid' ), $the_booking->get( 'currency' ) ); ?></td>
				</tr>

				<tr>
					<th><?php echo esc_html__( 'Balance Due:', 'awebooking' ); ?></th>
					<td><?php abrs_price( $the_booking->get( 'balance_due' ), $the_booking->get( 'currency' ) ); ?></td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="booking-section" style="padding: 0.5rem 0.75rem; border-top: solid 1px #eee;">
		<?php if ( $the_booking->is_editable() ) : ?>

			<a class="button abrs-button" href="<?php echo esc_url( abrs_admin_route( '/booking-room', [ 'refer' => $the_booking->get_id() ] ) ); ?>">
				<span><?php esc_html_e( 'Add room', 'awebooking' ); ?></span>
			</a>

			<a class="button abrs-button" href="<?php echo esc_url( abrs_admin_route( '/booking-service', [ 'refer' => $the_booking->get_id() ] ) ); ?>">
				<span><?php esc_html_e( 'Add service', 'awebooking' ); ?></span>
			</a>
		
			<?php do_action( 'abrs_booking_room_buttons' ); ?>

			<button class="button abrs-button abrs-fright" name="awebooking-calculate-totals" type="submit">
				<span><?php esc_html_e( 'Recalculate', 'awebooking' ); ?></span>
			</button>

		<?php else : ?>

			<span class="abrs-label tippy" title="<?php esc_html_e( 'Change the booking status back to "Pending" to edit this', 'awebooking' ); ?>"><?php esc_html_e( 'This booking is no longer editable', 'awebooking' ); ?></span>

		<?php endif; ?>
	</div>
</div>
