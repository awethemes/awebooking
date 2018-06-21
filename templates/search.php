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

/**
 * Search variables.
 *
 * @var \AweBooking\Frontend\Search\Search_Query $abrs_query
 * @var \AweBooking\Availability\Request         $res_request
 * @var \AweBooking\Availability\Query_Results   $abrs_results
 */
global $abrs_query, $res_request, $abrs_results;

get_header( 'awebooking' ); ?>

<?php
/**
 * The opening divs for the content.
 *
 * @hooked abrs_content_wrapper_before() - 10 (outputs opening divs for the content).
 */
do_action( 'abrs_before_main_content' );

while ( have_posts() ) : the_post(); // @codingStandardsIgnoreLine

	do_action( 'abrs_before_search_content' );

	if ( ! $res_request || ! $abrs_results || is_wp_error( $abrs_query->errors ) ) {
		abrs_get_template( 'search/error.php' );
	} elseif ( ! $abrs_results->has_items() ) {
		abrs_get_template( 'search/no-results.php', [ 'results' => $abrs_results ] );
	} else {
		abrs_get_template( 'search/results.php', [ 'results' => $abrs_results ] );
	}

	do_action( 'abrs_after_search_content' );

endwhile;

/**
 * Outputs closing divs for the content
 *
 * @hooked abrs_content_wrapper_after() - 10 (outputs closing divs for the content).
 */
do_action( 'abrs_after_main_content' );
?>

<?php get_footer( 'awebooking' ); // @codingStandardsIgnoreLine

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
