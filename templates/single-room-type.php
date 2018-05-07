<?php
/**
 * The Template for displaying all single room types
 *
 * This template can be overridden by copying it to yourtheme/awebooking/single-room-type.php.
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
		 * awebooking/before_main_content hook.
		 *
		 * @hooked awebooking_output_content_wrapper - 10 (outputs opening divs for the content)
		 */
		do_action( 'awebooking/before_main_content' );

		do_action( 'awebooking/template_notices' );
	?>

	<?php while ( have_posts() ) : the_post(); ?>

		<?php abrs_get_template_part( 'content', apply_filters( 'awebooking/content_single_layout', 'single-room-type' ) ); ?>

	<?php endwhile; // end of the loop. ?>

	<?php
		/**
		 * awebooking/after_main_content hook.
		 *
		 * @hooked awebooking_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'awebooking/after_main_content' );
	?>

<?php get_footer( 'awebooking' ); // @codingStandardsIgnoreLine

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
