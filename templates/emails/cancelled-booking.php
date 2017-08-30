<?php
/**
 * Cancelled booking email.
 *
 * This template can be overridden by copying it to yourtheme/awebooking/emails/cancelled-booking.php.
 *
 * @author 		Awethemes
 * @package 	AweBooking/Templates
 * @version     1.0.0
 */

printf( __( 'Your booking #%1$d from %2$s has been cancelled.', 'awebooking' ), $booking_id, $customer_first_name );
?>
<h2 style="margin-top: 50px;"><?php printf( esc_html__( 'Order #%s', 'awebooking' ), esc_html( $booking_id ) ); ?></h2>

<div class="table">
	<table>
		<thead>
			<tr>
				<th style="text-align: left;"><?php esc_html_e( 'Room', 'awebooking' ); ?></th>
				<th style="text-align: left;"><?php esc_html_e( 'Nights', 'awebooking' ); ?></th>
				<th style="text-align: left;"><?php esc_html_e( 'Guest', 'awebooking' ); ?></th>
				<th style="text-align: right;"><?php esc_html_e( 'Price', 'awebooking' ); ?></th>
			</tr>

		</thead>
	</table>
</div>

<?php foreach ( $booking_room_units as $key => $room_item ) : ?>
<div class="table">
	<table>
		<tbody>
			<tr>
				<td style="text-align: left;"><b><?php echo esc_html( $room_item->get_name() ); ?></b></td>
				<td style="text-align: left;"><?php printf( __( 'From %1$s to %2$s, %3$s nights', 'awebooking' ), $room_item->get_check_in(), $room_item->get_check_out(), $room_item->get_nights_stayed() ); // WPCS: xss ok. ?></td>
				<td style="text-align: left;"><?php $room_item->get_fomatted_guest_number(); ?></td>
				<td style="text-align: right;"><?php print $room_item->get_total(); // WPCS: xss ok. ?></td>
			</tr>
			
			<?php if ( $service_items = $booking->get_service_items()->where( 'parent_id', $room_item->get_id() ) ) : ?>
				<tr>
					<td style="text-align: left;"><b><?php esc_html_e( 'Extra services', 'awebooking' ); ?></b></td>
					<td colspan="2" style="text-align: left;">
						<?php $service_name = []; ?>
						<?php foreach ( $service_items as $service_item ) : ?>
							<?php $service_name[] = $service_item->get_name(); ?>
							<?php echo esc_html( implode( ', ', $service_name ) ); ?>
						<?php endforeach; ?>
					</td>
					<td style="text-align: right;"><?php echo 123;// TODO: ... ?></td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
</div>
<?php endforeach; ?>

<div class="table">
	<table>
		<thead>
			<tr>
				<th colspan="2" style="text-align: left;"><b><?php esc_html_e( 'Total', 'awebooking' ); ?></b></th>
				<th style="text-align: right;"><b><?php print $total_price; // WPCS: xss ok. ?></b></th>
			</tr>

		</thead>
	</table>
</div>
