<?php
/**
 * Output the service item.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/checkout/terms.php
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! apply_filters( 'abrs_checkout_show_terms', abrs_get_page_id( 'terms' ) > 0 ) ) {
	return;
}

?>

<div id="terms-and-conditions" class="checkout__section checkout__section--terms">
	<?php
	/**
	 * Terms and conditions hook used to inject content.
	 *
	 * @since 3.1.8
	 */
	do_action( 'abrs_checkout_terms_and_conditions' );
	?>

	<p class="nice-checkbox">
		<input type="checkbox" name="terms" id="term">
		<label for="term"><?php echo abrs_get_terms_and_conditions_checkbox_text(); // WPCS: XSS OK. ?></label>
	</p>
</div>
