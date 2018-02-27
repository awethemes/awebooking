<?php
/**
 * Output a single payment method (gateway).
 *
 * @author  awethemes
 * @package AweBooking/Templates
 * @version 3.0.0
 */

/* @vars $gateway */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<li class="awebooking-payment-method" data-payment-method="<?php echo esc_attr( $gateway->get_method() ); ?>">
	<input id="payment_method_<?php echo esc_attr( $gateway->get_method() ); ?>" type="radio" name="payment_method" value="<?php echo esc_attr( $gateway->get_method() ); ?>">

	<label class="awebooking-payment-method__label" for="payment_method_<?php echo esc_attr( $gateway->get_method() ); ?>">
		<?php print $gateway->get_title(); // WPCS: XSS OK. ?>
	</label>

	<?php if ( $description = $gateway->get_description() ) : ?>
		<div class="awebooking-payment-method__description">
			<?php echo wp_kses_post( wpautop( wptexturize( $description ) ) ); ?>
		</div>
	<?php endif ?>

	<div id="content_payment_method_<?php echo esc_attr( $gateway->get_method() ); ?>">
		<?php $gateway->print_payment_fields(); ?>
	</div>
</li><!-- /.awebooking-payment-method -->
