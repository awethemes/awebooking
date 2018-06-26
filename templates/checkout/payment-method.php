<?php
/**
 * Output a single payment method.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/checkout/payment-method.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$selected_gateway = abrs_session( 'selected_payment_method' );
if ( empty( $selected_gateway ) ) {
	$selected_gateway = $gateways->keys()->first();
}

?>

<li class="payment-method payment-method--<?php echo esc_attr( $gateway->get_method() ); ?>">
	<div class="nice-radio">
		<input id="payment_method_<?php echo esc_attr( $gateway->get_method() ); ?>" type="radio" class="payment-method__input" name="payment_method" value="<?php echo esc_attr( $gateway->get_method() ); ?>" <?php checked( $gateway->get_method(), $selected_gateway ); ?>>

		<label class="payment-method__label" for="payment_method_<?php echo esc_attr( $gateway->get_method() ); ?>">
			<?php echo $gateway->get_title(); // @codingStandardsIgnoreLine ?>
		</label>
	</div>

	<?php if ( $gateway->has_fields() || $gateway->get_description() ) : ?>
		<div class="payment-method__description payment_method_<?php echo esc_attr( $gateway->get_method() ); ?>">
			<?php $gateway->display_fields(); ?>
		</div>
	<?php endif; ?>
</li>
