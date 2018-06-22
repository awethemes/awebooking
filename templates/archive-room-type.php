<?php
/**
 * The template for displaying room types.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/archive-room-type.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header( 'awebooking' ); ?>

	<?php
	/**
	 * The opening divs for the content.
	 *
	 * @hooked abrs_content_wrapper_before() - 10 (outputs opening divs for the content).
	 */
	do_action( 'abrs_before_main_content' ); ?>
	<?php if ( have_posts() ) : ?>

		<?php
		while ( have_posts() ) : the_post(); // @codingStandardsIgnoreLine
			abrs_get_template_part( 'template-parts/loop/content', 'room-type' );
		endwhile; ?>

	<?php else : ?>
		<?php abrs_get_template( 'search/no-results.php' ); ?>
	<?php endif; ?>

	<?php
	/**
	 * Outputs closing divs for the content
	 *
	 * @hooked abrs_content_wrapper_after() - 10 (outputs closing divs for the content).
	 */
	do_action( 'abrs_after_main_content' );
	?>

<?php get_footer( 'awebooking' ); // @codingStandardsIgnoreLine

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
