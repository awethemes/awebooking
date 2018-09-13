<?php
/**
 * The template for displaying hotel description in the template-parts/hotel/content.php template
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/template-parts/hotel/description.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<div class="hotel__section hotel-description-section">
	<h4 class="hotel__section-title"><?php esc_html_e( 'Description', 'awebooking' ); ?></h4>

	<div class="hotel__content">
		<?php the_content(); ?>
	</div>
</div>
