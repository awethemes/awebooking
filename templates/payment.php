<?php
/**
 * The template for displaying payment.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/payment.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 *
 * @var \AweBooking\Model\Booking   $booking
 * @var \AweBooking\Gateway\Gateway $gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header( 'awebooking' );

/**
 * The opening divs for the content.
 *
 * @hooked abrs_content_wrapper_before() - 10 (outputs opening divs for the content).
 */
do_action( 'abrs_before_main_content' );

// Print the notices messages.
do_action( 'abrs_print_notices' );

?>

	<div class="awebooking-page awebooking-page--payment">
		<header>
			<h2><?php esc_html_e( 'Complete your reservation', 'awebooking' ); ?></h2>
			<p><?php esc_html_e( 'Your reservation has been reserved, just finish your last step!', 'awebooking' ); ?></p>
		</header>

		<section>
			<?php if ( isset( $expired_seconds, $expired_formatted ) && $expired_seconds > 0 ) : ?>
				<div id="timer-countdown" data-seconds="<?php echo esc_attr( $expired_seconds ); ?>">
					<strong><?php echo esc_html( $expired_formatted ); ?></strong>
				</div>
			<?php endif; ?>

			<?php abrs_get_template( 'checkout/overview.php', compact( 'booking' ) ); ?>
		</section>

		<form
			method="POST"
			id="checkout-form"
			class="checkout"
			action="<?php echo esc_url( abrs_route( '/payment/' . $booking->get_public_token() ) ); ?>">
			<?php wp_nonce_field( 'awebooking_payment_process', '_wpnonce', true ); ?>

			<ul id="payment-methods" class="payment-methods">
				<li class="payment-method selected">
					<div class="nice-radio">
						<input readonly checked type="radio" class="payment-method__input" name="payment_method" value="<?php echo esc_attr( $gateway->get_method() ); ?>" style="display: none;">

						<label class="payment-method__label" for="payment_method_<?php echo esc_attr( $gateway->get_method() ); ?>">
							<?php echo $gateway->get_title(); // @codingStandardsIgnoreLine ?>
						</label>
					</div>

					<?php if ( $gateway->has_fields() || $gateway->get_description() ) : ?>
						<div class="payment-method__description">
							<?php $gateway->display_fields(); ?>
						</div>
					<?php endif; ?>
				</li>
			</ul>

			<button type="submit" class="button button--primary" name="awebooking_submit">
				<?php esc_html_e( 'Process Payment', 'awebooking' ); ?>
			</button>
		</form>
	</div><!-- /.awebooking-page--checkout -->

<?php
/**
 * Outputs closing divs for the content
 *
 * @hooked abrs_content_wrapper_after() - 10 (outputs closing divs for the content).
 */
do_action( 'abrs_after_main_content' );

get_footer( 'awebooking' ); // @codingStandardsIgnoreLine

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
