<?php
/**
 * Sidebar
 *
 * This template can be overridden by copying it to yourtheme/awebooking/global/sidebar.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( is_active_sidebar( 'awebooking-sidebar' )  ) : ?>
	<aside id="secondary" class="sidebar widget-area" role="complementary">
		<?php dynamic_sidebar( 'awebooking-sidebar' ); ?>
	</aside><!-- .sidebar .widget-area -->
<?php endif; ?>
