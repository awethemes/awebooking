<?php
/**
 * The template for checkout page.
 *
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

do_action( 'awebooking/template_notices' );

?>

<form method="POST" class="awebooking-checkout-form" action="<?php echo esc_url( abrs_route( '/checkout' ) ); ?>" enctype="multipart/form-data">
	<?php wp_nonce_field( 'awebooking_checkout_process' ); ?>

	<button type="sumbit">Checkout</button>
</form>
