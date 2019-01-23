<?php
/**
 * The template for displaying submit button in the search-form.php template
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/template-parts/search-form/button.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<div class="searchbox__box searchbox__box--button">
	<div class="searchbox__box-wrap">
		<button type="submit" class="button button--primary searchbox__submit"><?php esc_html_e( 'Check Availability', 'awebooking' ); ?></button>
	</div>
</div>
