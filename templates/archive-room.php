<?php
/**
 * The template for displaying room types.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/archive-room.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header( 'awebooking' );

/**
 * The opening divs for the content.
 *
 * @hooked abrs_content_wrapper_before() - 10 (outputs opening divs for the content).
 */
do_action( 'abrs_before_main_content' );

/**
 * abrs_before_archive_loop hook.
 */
do_action( 'abrs_before_archive_loop' );

if ( have_posts() ) :
	while ( have_posts() ) : the_post(); // @codingStandardsIgnoreLine
		abrs_get_template_part( 'template-parts/archive/content', apply_filters( 'abrs_archive_room_layout', '' ) );
	endwhile;

	/**
	 * abrs_after_archive_loop hook.
	 *
	 * @hooked abrs_pagination - 10
	 */
	do_action( 'abrs_after_archive_loop' );
	?>
<?php
else :
	abrs_get_template_part( 'template-parts/archive/content', 'none' );
endif;

/**
 * Outputs closing divs for the content
 *
 * @hooked abrs_content_wrapper_after() - 10 (outputs closing divs for the content).
 */
do_action( 'abrs_after_main_content' );

/**
 * awebooking/sidebar hook.
 *
 * @hooked abrs_get_sidebar - 10
 */
do_action( 'abrs_sidebar' );

get_footer( 'awebooking' ); // @codingStandardsIgnoreLine

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
