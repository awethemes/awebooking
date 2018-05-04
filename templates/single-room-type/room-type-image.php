<?php
/**
 * Single Room type Image
 *
 * This template can be overridden by copying it to yourtheme/awebooking/single-room-type/room-type-image.php.
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
<?php if ( has_post_thumbnail() ) : ?>
	<div class="awebooking-room-type__image">
		<?php the_post_thumbnail( 'awebooking_single' ); ?>
	</div>
<?php else : ?>
	<div class="awebooking-room-type__image">
		<img src="<?php echo esc_url( awebooking_placeholder_img_src() ) ?>" alt="<?php echo esc_html__( 'Awaiting room type image', 'awebooking' ) ?>" class="wp-post-image" />
	</div>
<?php endif; ?>
<?php do_action( 'awebooking/room_type_thumbnails' ); ?>
