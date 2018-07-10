<?php
/**
 * The template for displaying room thumbnail in the template-parts/archive/content.php template
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/template-parts/archive/thumbnail.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="list-room__media">
	<a href="<?php echo esc_url( get_the_permalink() ); ?>">
		<?php abrs_template_room_thumbnail(); ?>
	</a>
</div>
