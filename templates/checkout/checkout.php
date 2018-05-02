<?php
/**
 * Output the checkout form (used for shortcode).
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/checkout/checkout.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'awebooking/template_notices' );

?>

<?php do_action( 'awebooking/before_checkout_form' ); ?>

<form id="checkout-form" method="POST" action="<?php echo esc_url( abrs_route( '/checkout' ) ); ?>" enctype="multipart/form-data">
	<?php wp_nonce_field( 'awebooking_checkout_process', '_wpnonce', true ); ?>

	<?php do_action( 'awebooking/checkout_booking_details' ); ?>

	<?php do_action( 'awebooking/checkout_guest_details' ); ?>

	<?php do_action( 'awebooking/checkout_payments' ); ?>
</form>

<?php do_action( 'awebooking/after_checkout_form' ); ?>
