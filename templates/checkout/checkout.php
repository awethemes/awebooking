<?php
/**
 * The Template for checkout page.
 *
 * This template can be overridden by copying it to yourtheme/awebooking/checkout.php.
 *
 * @author 		Awethemes
 * @package 	Awethemes/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<?php do_action( 'awebooking/template_notices' ); ?>

<form method="POST" action="<?php echo esc_attr( awebooking( 'url' )->route( 'checkout/process' ) ); ?>" id="awebooking-checkout-form" class="awebooking-checkout-form">
	<?php wp_nonce_field( 'awebooking-checkout' ); ?>

	<?php
	/**
	 * Hook: "awebooking/checkout/detail_tables"
	 *
	 * @hooked awebooking_template_checkout_general_informations - 10
	 */
	// do_action( 'awebooking/checkout/detail_tables' );

	/**
	 * Hook: "awebooking/checkout/customer_form
	 *
	 * @hooked awebooking_template_checkout_customer_form - 10
	 */
	do_action( 'awebooking/checkout/customer_form' );

	?>
</form>
