<?php
/**
 * The template for displaying room description in the template-parts/single/content.php template
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/template-parts/single/description.php.
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

<div class="room__section room-description-section">
	<h4 class="room__section-title"><?php esc_html_e( 'Description', 'awebooking' ); ?></h4>

	<div class="room__content">
		<?php the_content(); ?>
	</div>
</div>
