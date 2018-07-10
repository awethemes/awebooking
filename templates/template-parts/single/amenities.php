<?php
/**
 * The template for displaying room amenities in the template-parts/single/content.php template
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/template-parts/single/amenities.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $room_type;

$amenities = wp_get_post_terms( $room_type->get_id(), 'hotel_amenity' );

if ( ! $amenities ) {
	return;
}
?>

<div class="room__section room-amenities-section">
	<h4 class="room__section-title"><?php esc_html_e( 'Amenities', 'awebooking' ); ?></h4>

	<ul id="amenities-<?php echo absint( $room_type->get_id() ); ?>" class="room-amenities">
		<?php foreach ( $amenities as $amenity ) : ?>
			<li class="room-amenity">
				<?php if ( $icon = get_term_meta( $amenity->term_id, '_icon', true ) ) : ?>
					<span class="room-amenity__icon">
						<?php if ( 'svg' === $icon['type'] || 'image' === $icon['type'] ) : ?>
							<?php echo wp_get_attachment_image( $icon['icon'] ); ?>
						<?php else : ?>
							<i class="<?php echo esc_attr( $icon['type'] . ' ' . $icon['icon'] ); ?>"></i>
						<?php endif; ?>
					</span>
				<?php else : ?>
					<i class="aficon aficon-checkmark"></i>
				<?php endif; ?>
				<span class="room-amenity__title"><?php echo esc_html( $amenity->name ); ?></span>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
