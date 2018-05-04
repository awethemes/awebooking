<?php
/**
 * Customer details.
 *
 * This template can be overridden by copying it to yourtheme/awebooking/emails/customer-details.php.
 *
 * HOWEVER, on occasion AweBooking will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

$customer_first_name  = $booking['customer_first_name'];
$customer_last_name   = $booking['customer_last_name'];
$customer_email       = $booking->get_customer_email();
$customer_phone       = $booking['customer_phone'];
$customer_company     = $booking->get_customer_company();
$customer_note        = $booking['customer_note'];
?>
<h2><?php esc_html_e( 'Customer details', 'awebooking' ); ?></h2>
<div class="table">
	<table>
		<tbody>
			<tr>
				<td><strong><?php esc_html_e( 'First Name', 'awebooking' ); ?>:</strong></td>
				<td><?php echo esc_html( $customer_first_name ); ?></td>
			</tr>
			<tr>
				<td><strong><?php esc_html_e( 'Last Name', 'awebooking' ); ?>:</strong></td>
				<td><?php echo esc_html( $customer_last_name ); ?></td>
			</tr>
			<tr>
				<td><strong><?php esc_html_e( 'Email', 'awebooking' ); ?>:</strong></td>
				<td><?php echo esc_html( $customer_email ); ?></td>
			</tr>
			<tr>
				<td><strong><?php esc_html_e( 'Phone', 'awebooking' ); ?>:</strong></td>
				<td><?php echo esc_html( $customer_phone ); ?></td>
			</tr>
			<tr>
				<td><strong><?php esc_html_e( 'Company', 'awebooking' ); ?>:</strong></td>
				<td><?php echo esc_html( $customer_company ); ?></td>
			</tr>
			<?php if ( $customer_note ) : ?>
				<tr>
					<td><strong><?php esc_html_e( 'Note', 'awebooking' ); ?>:</strong></td>
					<td><?php echo esc_html( $customer_note ); ?></td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
</div>
