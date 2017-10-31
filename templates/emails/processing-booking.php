<?php
/**
 * Completed booking email.
 *
 * This template can be overridden by copying it to yourtheme/awebooking/emails/completed-booking.php.
 *
 * @author 		Awethemes
 * @package 	AweBooking/Templates
 * @version     3.0.0
 */
?>
<p><?php printf( __( "Hi there. Your recent booking on %s is being processed. Your booking details are shown below for your reference:", 'awebooking' ), get_option( 'blogname' ) ); ?></p>
<h2 style="margin-top: 50px;"><?php printf( esc_html__( 'Booking #%s', 'awebooking' ), esc_html( $booking_id ) ); ?></h2>

<div class="table">
	<table>
		<thead>
			<tr>
				<th style="text-align: left;"><?php esc_html_e( 'Room', 'awebooking' ); ?></th>
				<th style="text-align: left;"><?php esc_html_e( 'Nights', 'awebooking' ); ?></th>
				<th style="text-align: left;"><?php esc_html_e( 'Guest', 'awebooking' ); ?></th>
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

<h2><?php _e( 'Customer details', 'awebooking' ); ?></h2>
<ul>
	<li><strong><?php esc_html_e( 'First Name', 'awebooking' ); ?>:</strong> <span class="text"><?php echo esc_html( $customer_first_name ); ?></span></li>
	<li><strong><?php esc_html_e( 'Last Name', 'awebooking' ); ?>:</strong> <span class="text"><?php echo esc_html( $customer_last_name ); ?></span></li>
	<li><strong><?php esc_html_e( 'Email', 'awebooking' ); ?>:</strong> <span class="text"><?php echo esc_html( $customer_email ); ?></span></li>
	<li><strong><?php esc_html_e( 'Phone', 'awebooking' ); ?>:</strong> <span class="text"><?php echo esc_html( $customer_phone ); ?></span></li>
	<li><strong><?php esc_html_e( 'Company', 'awebooking' ); ?>:</strong> <span class="text"><?php echo esc_html( $customer_company ); ?></span></li>

	<?php if ( $customer_note ) : ?>
		<li><strong><?php esc_html_e( 'Note', 'awebooking' ); ?>:</strong> <span class="text"><?php echo esc_html( $customer_note ); ?></span></li>
	<?php endif; ?>
</ul>

