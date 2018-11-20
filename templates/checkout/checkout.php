<?php
/**
 * Output the checkout form.
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

<div class="hotel-content">
	<div class="hotel-content__main">
		<?php do_action( 'abrs_before_checkout_form' ); ?>

		<form id="checkout-form" class="checkout" method="POST" action="<?php echo esc_url( abrs_route( '/checkout' ) ); ?>" enctype="multipart/form-data">
			<?php wp_nonce_field( 'awebooking_checkout_process', '_wpnonce', true ); ?>

			<?php
			do_action( 'abrs_html_checkout_services' );

			do_action( 'abrs_html_checkout_booking_details' );

			do_action( 'abrs_html_checkout_guest_details' );

			do_action( 'abrs_html_checkout_payments' );
			?>
		</form>

		<?php do_action( 'abrs_after_checkout_form' ); ?>
	</div>

	<aside class="hotel-content__aside">
		<?php abrs_get_template( 'reservation/reservation.php' ); ?>
	</aside>
</div><!-- /.container -->
