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

/* @var \WP_Error $errors */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="search-rooms__errors notification notification--error">
	<?php echo wp_kses_post( $errors->get_error_message() ); ?>
</div><!-- /.search-rooms__errors-->
