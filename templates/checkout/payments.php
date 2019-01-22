<?php
/**
 * Show the payment methods.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/checkout/payments.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php if ( ! abrs_blank( $gateways ) ) : ?>

	<div id="payment_gateways" class="checkout__section checkout__section--gateways">
		<header class="checkout__section-header">
			<h3 class="checkout__section__title"><?php esc_html_e( 'Payment method', 'awebooking' ); ?></h3>
		</header>

		<ul id="payment-methods" class="payment-methods">
			<?php foreach ( $gateways as $gateway ) : ?>
				<?php abrs_get_template( 'checkout/payment-method.php', compact( 'gateway', 'gateways' ) ); ?>
			<?php endforeach ?>
		</ul>
	</div>

<?php endif ?>

<?php abrs_get_template( 'checkout/terms.php' ); ?>

<div id="submit_booking" class="checkout__section checkout__section--submit">
	<?php
	do_action( 'abrs_before_submit_button' );

	echo apply_filters( 'abrs_booking_button_html', '<button type="submit" class="button button--primary" name="awebooking_submit">' . esc_html( $button_text ) . '</button>' ); // @codingStandardsIgnoreLine

	do_action( 'abrs_after_submit_button' );
	?>
</div>
