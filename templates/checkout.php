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
	do_action( 'awebooking/checkout/detail_tables', $availability, $room_type );
?>

<?php
	/**
	 * awebooking/checkout/customer_form hook.
	 *
	 * @hooked abkng_template_checkout_customer_form - 10
	 */
	do_action( 'awebooking/checkout/customer_form' );
?>

<?php endif ?>
