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

do_action( 'awebooking/template_notices' ); 

/**
 * Hook: "awebooking/checkout/detail_tables"
 *
 * @hooked awebooking_template_checkout_general_informations - 10
 */
do_action( 'awebooking/checkout/detail_tables' );

/**
 * Hook: "awebooking/checkout/customer_form
 *
 * @hooked awebooking_template_checkout_customer_form - 10
 */
do_action( 'awebooking/checkout/customer_form' ); ?>
