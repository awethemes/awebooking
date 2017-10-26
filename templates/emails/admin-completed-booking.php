<?php
/**
 * Admin completed booking email.
 *
 * This template can be overridden by copying it to yourtheme/awebooking/emails/admin-completed-booking.php.
 *
 * @author 		Awethemes
 * @package 	AweBooking/Templates
 * @version     3.0.0
 */
?>
<p><?php printf( __( 'The booking #%1$d from %2$s has been completed. The order was as follows:', 'awebooking' ), $booking_id, $customer_first_name ); ?></p>
<h2>
	<a class="link" href="<?php echo esc_url( get_edit_post_link( $booking_id ) ); ?>">
		<?php printf( __( 'Booking #%s', 'awebooking' ), $booking_id ); ?>
	</a>
</h2>

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
