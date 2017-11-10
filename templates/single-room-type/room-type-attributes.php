<?php
/**
 * Room type attributes
 *
 * Used by list_attributes() in the room type class.
 *
 * This template can be overridden by copying it to yourtheme/awebooking/single-room-type/room-type-attributes.php.
 *
 * @author 		Awethemes
 * @package 	Awethemes/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$amenities = wp_get_post_terms( get_the_ID(), 'hotel_amenity' );
?>
<?php foreach ( $amenities as $amenity ) : ?>
	<div class="awebooking-amenities__item">
		<?php if ( $icon = get_term_meta( $amenity->term_id, '_icon', true ) ) : ?>
			<span class="awebooking-amenities__icon">
				<?php if ( 'svg' === $icon['type'] || 'image' === $icon['type'] ) : ?>
					<?php echo wp_get_attachment_image( $icon['icon'] ); ?>
				<?php else : ?>
					<i class="<?php echo esc_attr( $icon['type'] . ' ' . $icon['icon'] ); ?>"></i>
				<?php endif; ?>
			</span>
		<?php endif; ?>
		<h3 class="awebooking-amenities__title"><?php echo $amenity->name; ?></h3>
		<p class="awebooking-amenities__desc"><?php echo $amenity->description; ?></p>
	</div>
<?php endforeach; ?>
