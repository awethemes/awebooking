<?php
/**
 * Customer details.
 *
 * This template can be overridden by copying it to yourtheme/awebooking/emails/customer-details.php.
 *
 * @author 		Awethemes
 * @package 	AweBooking/Templates
 * @version     3.0.0
 */

$customer_first_name  = $booking['customer_first_name'];
$customer_last_name   = $booking['customer_last_name'];
$customer_email       = $booking->get_customer_email();
$customer_phone       = $booking['customer_phone'];
$customer_company     = $booking->get_customer_company();
$customer_note        = $booking['customer_note'];

?>
<h2><?php esc_html_e( 'Customer details', 'awebooking' ); ?></h2>

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
