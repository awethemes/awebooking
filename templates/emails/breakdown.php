<?php
/**
 * Breakdown.
 *
 * This template can be overridden by copying it to yourtheme/awebooking/emails/breakdown.php.
 *
 * @author   Awethemes
 * @package  AweBooking/Templates
 * @version  3.0.0
 */

$booking_id           = $booking->get_id();
$total_price          = (string) $booking->get_total();
?>
<div class="table">
	<table>
		<thead>
			<tr>
				<th style="text-align: left;"><?php esc_html_e( 'Room', 'awebooking' ); ?></th>
				<th style="text-align: left;"><?php esc_html_e( 'Nights', 'awebooking' ); ?></th>
				<th style="text-align: right;"><?php esc_html_e( 'Guest', 'awebooking' ); ?></th>
			</tr>
		</thead>
	</table>
</div>

<?php foreach ( $booking->get_line_items() as $key => $room_item ) : ?>
<div class="table">
	<table>
		<tbody>
			<tr>
				<td style="text-align: left;"><b><?php echo esc_html( $room_item->get_name() ); ?></b></td>
				<td style="text-align: left; padding: 10px 5px;"><?php printf( __( 'From %1$s to %2$s, %3$s nights', 'awebooking' ), $room_item->get_check_in(), $room_item->get_check_out(), $room_item->get_nights_stayed() ); // WPCS: xss ok. ?></td>
				<td style="text-align: right;"><?php $room_item->get_fomatted_guest_number(); ?></td>
			</tr>

			<?php if ( $service_items = $booking->get_service_items()->where( 'parent_id', $room_item->get_id() ) ) : ?>
				<tr>
					<td style="text-align: left;"><b><?php esc_html_e( 'Extra services', 'awebooking' ); ?></b></td>
					<td colspan="2" style="text-align: left; padding: 10px 5px;">
						<?php $service_name = []; ?>
						<?php foreach ( $service_items as $service_item ) : ?>
							<?php $service_name[] = $service_item->get_name(); ?>
							<?php echo esc_html( implode( ', ', $service_name ) ); ?>
						<?php endforeach; ?>
					</td>
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
