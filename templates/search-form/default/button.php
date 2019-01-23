<?php
/**
 * The template for displaying submit button in the search-form.php template
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/template-parts/search-form/button.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<button type="submit" class="button button--primary abrs-searchbox__submit">
	<?php esc_html_e( 'Check Availability', 'awebooking' ); ?>
</button>
