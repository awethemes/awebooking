<?php
/**
 * Output the guest form controls.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/checkout/form-guest-details.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$customer_controls = $controls->get_section( 'customer' );

if ( empty( $customer_controls->fields ) ) {
	return;
}

?>

<div id="guest-details" class="checkout__section checkout__section--guest-details">
	<header class="checkout__section-header">
		<h3 class="checkout__section__title"><?php esc_html_e( 'Guest details', 'awebooking' ); ?></h3>
	</header>

	<?php do_action( 'abrs_before_guest_details' ); ?>

	<div class="guest-details-fields">
		<?php foreach ( $customer_controls->fields as $field_args ) : ?>

			<?php $controls->show_field( $field_args ); ?>

		<?php endforeach; ?>
	</div>

	<?php do_action( 'abrs_after_guest_details' ); ?>
</div>
