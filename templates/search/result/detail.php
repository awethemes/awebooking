<?php
/**
 * This template show the search result detail.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/search/result/detail.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* @var \AweBooking\Model\Room_Type $room_type */
/* @var \AweBooking\Availability\Room_Rate $room_rate */

?>

<div class="roommaster-detail" style="display: none;">
	<div class="columns">
		<div class="column-3">
			<?php
			if ( has_post_thumbnail( $room_type->get_id() ) ) {
				echo get_the_post_thumbnail( $room_type->get_id(), 'awebooking_archive' );
			}
			?>
		</div>

		<div class="column-9">
			<div class="roommaster-tab tabs-main">
				<ul class="roommaster-tab__list tabs-main-list">
					<li class="active" rel="short-description-<?php echo absint( $room_type->get_id() ); ?>">
						<?php esc_html_e( 'Short description', 'awebooking' ); ?>
					</li>
					<li rel="amenities-<?php echo absint( $room_type->get_id() ); ?>">
						<?php esc_html_e( 'Amenities', 'awebooking' ); ?>
					</li>
					<div class="tabs-active-divider"></div>
				</ul>

				<div class="roommaster-tab__container tabs-main-container">
					<div id="short-description-<?php echo absint( $room_type->get_id() ); ?>" class="tabs-main-content">
						<?php echo esc_html( $room_type->get( 'short_description' ) ); ?>
					</div>
					<ul id="amenities-<?php echo absint( $room_type->get_id() ); ?>" class="tabs-main-content">
						<?php $amenities = wp_get_post_terms( $room_type->get_id(), 'hotel_amenity' ); ?>
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
			</div>
		</div>
	</div>
</div>
