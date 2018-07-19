<?php
/**
 * The template part for displaying content that rooms cannot be found
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/template-parts/archive/content-none.php.
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

<div class="list-room__none list-room__empty list-room__box">
	<div class="list-room__box-icon">
		<img src="<?php echo esc_url( abrs_plugin_url( '/assets/img/no-rooms.svg' ) ); ?>" alt="<?php esc_attr_e( 'No rooms available', 'awebooking' ); ?>">
	</div>

	<div class="list-room__box-title">
		<?php esc_html_e( 'No rooms available', 'awebooking' ); ?>
	</div>
</div><!-- /.list-room__none -->
