<?php
/* @vars $calr, $event, $calendar, $scheduler, $attributes */

// Get the booking ID.
$booking = $event->get_value();

// This mean we have some days in state is marked as have booking
// but missing in boooking data. Need some way to resolve this.
if ( ! $the_booking = abrs_get_booking( $booking ) ) {
	return; // TODO: ...
}

// Ignore cancelled booking.
if ( 'awebooking-cancelled' === $the_booking['status'] ) {
	return;
}

$status = $the_booking->get( 'status' );

// Add the booking status in the wrap class.
$attributes['class'] .= ' ' . $status;

?><div <?php echo abrs_html_attributes( $attributes ); // WPCS: XSS OK. ?>>
	<a class="scheduler-inline-text" href="<?php echo esc_url( get_edit_post_link( $the_booking->get_id() ) ); ?>" target="_blank">
		<span class="">#<?php echo esc_html( $the_booking->get_booking_number() ); ?></span>
		<span style="display: inline-block; margin: 0 2px;">&middot;</span>
		<span><?php echo esc_html( $the_booking->get_customer_fullname() ); ?></span>
	</a>

	<div style="display: none;">
		<div class="js-tippy-html abrs-pt1" style="width: 380px;">
			<div class="abrs-pb1 abrs-plr1 abrs-text-left">
				<a class="" href="<?php echo esc_url( get_edit_post_link( $the_booking->get_id() ) ); ?>" target="_blank">
					<strong class="">#<?php echo esc_html( $the_booking->get_booking_number() ); ?></strong>
				</a>&nbsp;

				<?php printf( '<mark class="booking-status abrs-label %s"><span>%s</span></mark>', esc_attr( sanitize_html_class( $status . '-color' ) ), esc_html( abrs_get_booking_status_name( $status ) ) ); ?>

				<div class="abrs-fright">
					<?php if ( ( $nights_stay = $the_booking->get( 'nights_stay' ) ) == -1 ) : ?>
						<a class="scheduler-inline-text" href="<?php echo esc_url( get_edit_post_link( $the_booking->get_id() ) ); ?>" title="<?php esc_attr_e( 'Length of stay varies, see each room.', 'awebooking' ); ?>" target="_blank">
							<span class="dashicons dashicons-info"></span>
						</a>
					<?php else : ?>
						<span class="abrs-badge"><?php echo esc_html( abrs_format_date( $the_booking->get_check_in_date() ) ); ?></span>
						<span class="abrs-badge"><?php echo esc_html( abrs_format_date( $the_booking->get_check_out_date() ) ); ?></span>
					<?php endif; ?>
				</div>
			</div>

			<?php abrs_admin_template_part( 'booking/html-customer-details.php', [ 'booking' => $the_booking ] ); ?>

			<table class="awebooking-table abrs-booking-totals" style="width: 100% !important; float: none; background: #f9f9f9;">
				<tbody>
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
	</div>
</div>
