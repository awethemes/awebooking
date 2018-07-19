<?php
/**
 * The template for displaying pagination
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/template-parts/archive/pagination.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wp_query;

if ( $wp_query->max_num_pages <= 1 ) {
	return;
}

?>

<nav class="awebooking-pagination">
	<?php print paginate_links(); // WPCS: xss ok. ?>
</nav>
