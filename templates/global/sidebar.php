<?php
/**
 * Sidebar
 *
 * This template can be overridden by copying it to yourtheme/awebooking/global/sidebar.php.
 *
 * @author 		Awethemes
 * @package 	Awethemes/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( is_active_sidebar( 'awebooking-sidebar' )  ) : ?>
	<aside id="secondary" class="sidebar widget-area" role="complementary">
		<?php dynamic_sidebar( 'awebooking-sidebar' ); ?>
	</aside><!-- .sidebar .widget-area -->
<?php endif; ?>
