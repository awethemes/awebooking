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

?>

<?php do_action( 'abrs_print_notices' ); ?>

<div class="hotel-content">
	<div class="hotel-content__main">
		<?php do_action( 'abrs_before_checkout_form' ); ?>

		<form id="checkout-form" method="POST" action="<?php echo esc_url( abrs_route( '/checkout' ) ); ?>" enctype="multipart/form-data">
			<?php wp_nonce_field( 'awebooking_checkout_process', '_wpnonce', true ); ?>

			<?php do_action( 'abrs_html_checkout_booking_details' ); ?>

			<?php do_action( 'abrs_html_checkout_guest_details' ); ?>

			<?php do_action( 'abrs_html_checkout_payments' ); ?>
		</form>

		<?php do_action( 'abrs_after_checkout_form' ); ?>
	</div>

	<aside class="hotel-content__aside">
		<?php abrs_get_template( 'reservation/booked.php' ); ?>
	</aside>
</div><!-- /.container -->
