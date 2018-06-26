<?php
/**
 * The template for displaying search results.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/single-room.php.
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

// Print the notices messages.
do_action( 'abrs_print_notices' );

?>

	<div class="awebooking-page awebooking-page--checkout">
		<?php while ( have_posts() ) : the_post(); // @codingStandardsIgnoreLine

			do_action( 'abrs_before_checkout_content' );

			abrs_checkout()->output();

			do_action( 'abrs_after_checkout_content' );

		endwhile; // @codingStandardsIgnoreLine. ?>
	</div><!-- /.awebooking-page--checkout -->

<?php
/**
 * Outputs closing divs for the content
 *
 * @hooked abrs_content_wrapper_after() - 10 (outputs closing divs for the content).
 */
do_action( 'abrs_after_main_content' );

get_footer( 'awebooking' ); // @codingStandardsIgnoreLine

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
