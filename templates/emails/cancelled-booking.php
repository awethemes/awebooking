<?php
/**
 * Cancelled booking email.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/emails/cancelled-booking.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abrs_mailer()->header( $email );

?>

<div class="body-content">
	<?php echo wp_kses_post( wpautop( wptexturize( $content ) ) ); ?>
</div>

<?php

/**
 * Print the booking details.
 *
 * @param \AweBooking\Model\Booking  $booking The booking instance.
 * @param \AweBooking\Email\Mailable $email   The mailable instance.
 *
 * @hooked \AweBooking\Email\Mailer::template_hotel_address()
 * @hooked \AweBooking\Email\Mailer::template_customer_details()
 * @hooked \AweBooking\Email\Mailer::template_booking_details()
 */
do_action( 'awebooking_email_booking_details', $booking, $email );

abrs_mailer()->footer( $email );
