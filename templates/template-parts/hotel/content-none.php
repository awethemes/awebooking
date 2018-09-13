<?php
/**
 * The template part for displaying content that hotels cannot be found
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/template-parts/hotel/content-none.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="list-hotel__none list-hotel__empty list-hotel__box">
	<div class="list-hotel__box-icon">
		<img src="<?php echo esc_url( abrs_plugin_url( '/assets/img/no-rooms.svg' ) ); ?>" alt="<?php esc_attr_e( 'No hotels available', 'awebooking' ); ?>">
	</div>

	<div class="list-hotel__box-title">
		<?php esc_html_e( 'No hotels available', 'awebooking' ); ?>
	</div>
</div><!-- /.list-hotel__none -->
