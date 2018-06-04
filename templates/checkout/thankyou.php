<?php
/**
 * Show the payment methods.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/checkout/thankyou.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><div class="woocommerce-booking">
	<?php if ( null !== $booking ) : ?>

		<?php if ( 'failed' === $booking->get_status() ) : ?>

			<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-booking-failed"><?php _e( 'Unfortunately your booking cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce' ); ?></p>

			<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-booking-failed-actions">
				<a href="<?php echo esc_url( $booking->get_checkout_payment_url() ); ?>" class="button pay"><?php _e( 'Pay', 'woocommerce' ) ?></a>
				<?php if ( is_user_logged_in() ) : ?>
					<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php _e( 'My account', 'woocommerce' ); ?></a>
				<?php endif; ?>
			</p>

		<?php else : ?>

			<p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-booking-received"><?php echo apply_filters( 'woocommerce_thankyou_booking_received_text', __( 'Thank you. Your booking has been received.', 'woocommerce' ), $booking ); ?></p>

			<ul class="woocommerce-booking-overview woocommerce-thankyou-booking-details booking_details">

				<li class="woocommerce-booking-overview__booking booking">
					<?php _e( 'Order number:', 'woocommerce' ); ?>
					<strong><?php echo $booking->get_booking_number(); ?></strong>
				</li>

				<li class="woocommerce-booking-overview__date date">
					<?php _e( 'Date:', 'woocommerce' ); ?>
					<strong><?php echo ( $booking->get_date_created() ); ?></strong>
				</li>

				<?php if ( is_user_logged_in() && $booking->get( 'customer_id' ) === get_current_user_id() && $booking->get( 'customer_email' ) ) : ?>
					<li class="woocommerce-booking-overview__email email">
						<?php _e( 'Email:', 'woocommerce' ); ?>
						<strong><?php echo $booking->get( 'customer_email' ); ?></strong>
					</li>
				<?php endif; ?>

				<li class="woocommerce-booking-overview__total total">
					<?php _e( 'Total:', 'woocommerce' ); ?>
					<strong><?php echo $booking->get( 'total' ); ?></strong>
				</li>

				<?php if ( @$booking->get_payment_method_title() ) : ?>
					<li class="woocommerce-booking-overview__payment-method method">
						<?php _e( 'Payment method:', 'woocommerce' ); ?>
						<strong><?php echo wp_kses_post( $booking->get_payment_method_title() ); ?></strong>
					</li>
				<?php endif; ?>
			</ul>

		<?php endif; ?>

		<?php do_action( 'woocommerce_thankyou', $booking->get_id() ); ?>

	<?php else : ?>

		<p class="notification is-success woocommerce-thankyou-booking-received"><?php echo apply_filters( 'woocommerce_thankyou_booking_received_text', __( 'Thank you! Your booking has been received.', 'woocommerce' ), null ); ?></p>

	<?php endif; ?>

</div>
