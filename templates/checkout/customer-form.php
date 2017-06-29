<?php
/**
 * The Template for displaying check form.
 *
 * This template can be overridden by copying it to yourtheme/awebooking/checkout/customer-form.php.
 *
 * @author 		awethemes
 * @package 	AweBooking/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div class="clearfix clear">
	<div class="awebooking-billing-fields">
		<h2 class="awebooking-checkout-form__title"><?php esc_html_e( 'Booking Details', 'awebooking' ); ?></h2>

		<div class="awebooking-field form-row-first">
			<label><?php esc_html_e( 'First Name', 'awebooking' ); ?> <abbr class="required" title="required">*</abbr></label>
			<input type="text" name="customer_first_name" class="awebooking-input" required="required">
		</div>

		<div class="awebooking-field form-row-last">
			<label><?php esc_html_e( 'Last Name', 'awebooking' ); ?> <abbr class="required" title="required">*</abbr></label>
			<input type="text" name="customer_last_name" class="awebooking-input" required="required">
		</div>

		<div class="awebooking-field form-row-first">
			<label><?php esc_html_e( 'Email Address', 'awebooking' ); ?> <abbr class="required" title="required">*</abbr></label>
			<input type="email" name="customer_email" class="awebooking-input" required="required">
		</div>

		<div class="awebooking-field form-row-last">
			<label><?php esc_html_e( 'Phone', 'awebooking' ); ?> <abbr class="required" title="required">*</abbr></label>
			<input type="text" name="customer_phone" class="awebooking-input" required="required">
		</div>

		<div class="awebooking-field">
			<label><?php esc_html_e( 'Company Name', 'awebooking' ); ?></label>
			<input type="text" name="customer_company" class="awebooking-input">
		</div>
	</div>

	<div class="awebooking-billing-fields awebooking-billing-fields--right">
		<h2 class="awebooking-checkout-form__title"><?php esc_html_e( 'Additional Information', 'awebooking' ); ?></h2>

		<div class="awebooking-field">
			<label><?php esc_html_e( 'Note', 'awebooking' ); ?></label>
			<textarea name="customer_note"></textarea>
		</div>
	</div>
</div>
