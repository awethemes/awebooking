<?php
/**
 * The Template for checkout page.
 *
 * This template can be overridden by copying it to yourtheme/awebooking/checkout.php.
 *
 * @author 		Awethemes
 * @package 	Awethemes/Templates
 * @version     1.0.0
 */

use AweBooking\Support\Formatting;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<?php do_action( 'awebooking/template_notices' ); ?>

<?php if ( isset( $availability ) && $availability->available() ) : ?>

	<?php
	/**
	 * awebooking/checkout/detail_tables hook.
	 *
	 * @hooked abkng_template_checkout_general_informations - 10
	 */
	do_action( 'awebooking/checkout/detail_tables', $availability, $room_type ); ?>

	<form id="awebooking-checkout-form" class="awebooking-checkout-form" method="POST">
		<?php wp_nonce_field( 'awebooking-checkout-nonce' ); ?>
		<input type="hidden" name="awebooking-action" value="checkout">

		<?php
		/**
		 * Hook: 'awebooking/checkout/customer_form'.
		 *
		 * @hooked abkng_template_checkout_customer_form - 10
		 */
		do_action( 'awebooking/checkout/customer_form', $availability ); ?>

		<?php
		/**
		 * Hook before display submit form.
		 */
		do_action( 'awebooking/checkout/before_submit_form', $availability ); ?>

		<button type="submit" class="button" data-type="awebooking"><?php esc_html_e( 'Submit', 'awebooking' ); ?></button>
	</form>

<?php endif ?>
