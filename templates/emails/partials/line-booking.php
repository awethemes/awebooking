<?php
/**
 * Display the details in a booking.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/emails/partials/line-booking.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* @var \AweBooking\Model\Booking $booking */
?>

<table class="table-booking" width="100%" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th><?php esc_html_e( 'Room', 'awebooking' ); ?></th>
			<th><?php esc_html_e( 'Reservation', 'awebooking' ); ?></th>
			<th class="total-column"><?php esc_html_e( 'Price', 'awebooking' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $booking->get_line_items() as $item ) : ?>
			<?php $timespan = $item->get_timespan(); ?>
			<tr>
				<td><?php echo esc_html( $item->get_name() ); // WPCS: XSS OK. ?></td>
				<td>
					<p>
					<?php
					/* translators: %1$s check-in, %2$s check-out */
					printf( esc_html_x( '%1$s - %2$s', 'booking dates', 'awebooking' ),
						abrs_format_date( $timespan->get_start_date() ),
						abrs_format_date( $timespan->get_end_date() )
					); // WPCS: xss ok.
					?>
					</p>
					<p><?php echo abrs_ngettext_nights( $item->get_nights_stayed() ); // WPCS: XSS OK. ?></p>
					<p><?php echo abrs_format_guest_counts( $item->get_guests() ); // WPCS: XSS OK. ?></p>
				</td>

				<td class="total-column">
					<?php abrs_price( $item->get_total() ); ?>

					<?php if ( $item->get( 'total_tax' ) ) : ?>
						<br><?php esc_html_e( 'TAX:', 'awebooking' ); ?> <?php abrs_price( $item->get( 'total_tax' ) ); ?>
					<?php endif; ?>
				</td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>

<table class="table-booking" width="100%" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th><?php esc_html_e( 'Services', 'awebooking' ); ?></th>
			<th class="total-column"></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $booking->get_services() as $item ) : ?>
			<tr>
				<td>
					<?php echo esc_html( $item->get_name() ); // WPCS: XSS OK. ?>
				</td>

				<td class="total-column">
					<?php
					printf( /* translators: %1$s quantity, %2$s unit price */
						esc_html_x( '%1$s x %2$s', 'admin booking service price', 'awebooking' ),
						absint( $item->get( 'quantity' ) ),
						abrs_format_price( $item->get( 'price' ) )
					); // WPCS: xss ok.
					?>

					<p style="text-align: right;"><?php abrs_price( $item->get_total() ); ?></p>
				</td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>

<?php $fees = $booking->get_fees(); ?>
<?php if ( count( $fees ) > 0 ) : ?>
	<table class="table-booking" width="100%" cellpadding="0" cellspacing="0">
		<tbody>
			<?php foreach ( $fees as $item ) : ?>
				<tr>
					<th><?php echo esc_html( $item->get_name() ); ?></th>
					<td class="total-column"><?php abrs_price( $item->get_total() ); ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>

<?php do_action( 'abrs_email_booking_line_items', $booking, $email ); ?>

<table class="table-booking-totals">
	<tbody>
		<tr>
			<th><?php esc_html_e( 'Subtotal', 'awebooking' ); ?></th>
			<td class="total-column"><?php abrs_price( $booking->get_subtotal() ); ?></td>
		</tr>

		<tr>
			<th><?php esc_html_e( 'Total', 'awebooking' ); ?></th>
			<td class="total-column"><?php abrs_price( $booking->get_total() ); ?></td>
		</tr>

		<tr>
			<th><?php esc_html_e( 'Paid', 'awebooking' ); ?></th>
			<td class="total-column"><?php abrs_price( $booking->get_paid() ); ?></td>
		</tr>

		<tr>
			<th><?php esc_html_e( 'Balance Due', 'awebooking' ); ?></th>
			<td class="total-column"><?php abrs_price( $booking->get_balance_due() ); ?></td>
		</tr>
	</tbody>
</table>
