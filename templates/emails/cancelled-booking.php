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
				<th colspan="2" style="text-align: left;"><?php printf( esc_html__( 'Room type: %s', 'awebooking' ), esc_html( $room_name ) ); ?></th>
				<th style="text-align: right;"><?php esc_html_e( 'Price', 'awebooking' ); ?></th>
			</tr>

		</thead>
		<tbody>
			<tr>
				<td colspan="3" style="text-align: left;"><b><?php esc_html_e( 'Detail', 'awebooking' ); ?></b></td>
			</tr>

			<tr>
				<td colspan="2" style="text-align: left;"><?php printf( __( 'From %1$s to %2$s, %3$s nights', 'awebooking' ), $check_in, $check_out, $nights ); // WPCS: xss ok. ?></td>
				<td style="text-align: right;"><?php print $room_type_price; // WPCS: xss ok. ?></td>
			</tr>

			<tr>
				<td colspan="3" style="text-align: left;"><b><?php esc_html_e( 'Extra services', 'awebooking' ); ?></b></td>
			</tr>

			<tr>
				<td colspan="2" style="text-align: left;"><?php echo esc_html( implode( $extra_services_name , ', ') ); ?></td>
				<td style="text-align: right;"><?php print $extra_services_price; // WPCS: xss ok. ?></td>
			</tr>

		</tbody>
	</table>
</div>

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

