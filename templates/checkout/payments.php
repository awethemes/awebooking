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

	<div id="payment" class="checkout__section checkout__section--payment">
		<header class="section-header">
			<h3 class="section-header__title"><?php esc_html_e( 'Payment method', 'awebooking' ); ?></h3>
		</header>

		<ul class="payment-methods">
			<?php foreach ( $gateways as $gateway ) : ?>
				<?php abrs_get_template( 'checkout/payment-method.php', compact( 'gateway' ) ); ?>
			<?php endforeach ?>
		</ul>
	</div>

<?php endif ?>

<div id="submit_booking" class="checkout__section checkout__section--submit">
	<?php
	do_action( 'awebooking/before_submit_button' );

	echo apply_filters( 'awebooking/booking_button_html', '<button type="submit" class="button button--booknow" name="awebooking_submit">' . esc_html( $button_text ) . '</button>' ); // @codingStandardsIgnoreLine

	do_action( 'awebooking/after_submit_button' );
	?>
</div>
