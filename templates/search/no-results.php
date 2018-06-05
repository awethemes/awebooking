<?php
/**
 * The template part for displaying a message that rooms cannot be found.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/search/no-results.php.
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

<div class="search-rooms-timeout">
	<p><?php echo esc_html__( 'Sorry, we have no rooms on your dates.', 'awebooking' ); ?></p>
</div><!-- /.search-rooms-timeout -->
