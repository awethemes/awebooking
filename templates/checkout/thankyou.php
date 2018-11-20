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

		<ul class="awebooking-booking-overview awebooking-thankyou-booking-details booking_details">
			<li class="awebooking-booking-overview__booking booking">
				<span><?php esc_html_e( 'Reservation ID:', 'awebooking' ); ?></span>
				<strong><?php echo esc_html( $booking->get_booking_number() ); ?></strong>
			</li>

			<li class="awebooking-booking-overview__date date">
				<span><?php esc_html_e( 'Date:', 'awebooking' ); ?></span>
				<strong><?php echo esc_html( abrs_format_date( $booking->get( 'date_created' ) ) ); ?></strong>
			</li>

			<?php if ( is_user_logged_in() && $booking->get( 'customer_email' ) && $booking->get( 'customer_id' ) === get_current_user_id() ) : ?>
				<li class="awebooking-booking-overview__email email">
					<span><?php esc_html_e( 'Email:', 'awebooking' ); ?></span>
					<strong><?php echo esc_html( $booking->get( 'customer_email' ) ); ?></strong>
				</li>
			<?php endif; ?>

			<?php if ( $payment_item = $booking->get_payments()->last() ) : ?>
				<li class="awebooking-booking-overview__payment-method method">
					<?php esc_html_e( 'Payment method:', 'awebooking' ); ?>
					<strong><?php echo wp_kses_post( $payment_item->get_method_title() ); ?></strong>
				</li>
			<?php endif; ?>

			<li class="awebooking-booking-overview__total total">
				<?php esc_html_e( 'Total:', 'awebooking' ); ?>
				<strong><?php abrs_price( $booking->get( 'total' ), $booking->get( 'currency' ) ); ?></strong>
			</li>
		</ul>

	<?php endif; ?>

	<?php do_action( 'awebooking_thankyou', $booking->get_id(), $last_payment ); ?>

</div>
