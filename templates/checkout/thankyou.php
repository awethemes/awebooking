<?php
/**
 * Show the payment methods.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/checkout/thankyou.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! $booking instanceof AweBooking\Model\Booking ) {
	return;
}

$last_payment = abrs_get_last_booking_payment( $booking );

?><div class="awebooking-booking">

	<?php if ( 'cancelled' === $booking->get_status() ) : ?>

		<p class=""><?php esc_html_e( 'Unfortunately, your booking has been cancelled.', 'awebooking' ); ?></p>

	<?php else : ?>

		<?php if ( ! isset( $_REQUEST['error'] ) ) : ?>

			<div class="notification notification--success">
				<p><?php esc_html_e( 'Thank you. Your booking has been received.', 'awebooking' ); ?></p>
			</div>

		<?php endif; ?>

		<?php abrs_get_template( 'checkout/overview.php', compact( 'booking' ) ); ?>

	<?php endif; ?>

	<?php do_action( 'awebooking_thankyou', $booking->get_id(), $last_payment ); ?>

</div>
