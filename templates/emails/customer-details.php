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

<table class="table table--customer" width="100%" cellpadding="0" cellspacing="0">
	<tbody>
		<tr>
			<td><?php esc_html_e( 'First Name', 'awebooking' ); ?></td>
			<td><?php echo esc_html( $customer_first_name ); ?></td>
		</tr>

		<tr>
			<td><?php esc_html_e( 'Last Name', 'awebooking' ); ?></td>
			<td><?php echo esc_html( $customer_last_name ); ?></td>
		</tr>

		<?php if ( $booking['customer_address'] ) : ?>
			<tr>
				<td><?php esc_html_e( 'Address', 'awebooking' ); ?></td>
				<td> <?php echo esc_html( $booking->get( 'customer_address' ) ); ?> <?php echo esc_html( $booking->get( 'customer_address_2' ) ); ?></td>
			</tr>
		<?php endif ?>

		<tr>
			<td><?php esc_html_e( 'Email', 'awebooking' ); ?></td>
			<td><?php echo esc_html( $customer_email ); ?></td>
		</tr>

		<tr>
			<td><?php esc_html_e( 'Phone', 'awebooking' ); ?></td>
			<td><?php echo esc_html( $customer_phone ); ?></td>
		</tr>

		<tr>
			<td><?php esc_html_e( 'Company', 'awebooking' ); ?></td>
			<td><?php echo esc_html( $customer_company ); ?></td>
		</tr>
	</tbody>
</table>
