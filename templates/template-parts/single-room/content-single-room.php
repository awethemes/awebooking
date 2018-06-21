<?php
/**
 * The template for displaying room_type content in the single-room_type.php template
 *
 * This template can be overridden by copying it to yourtheme/awebooking/content-single-room_type.php.
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

do_action( 'abrs_print_notices' );

if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}
?>

<div id="room-<?php the_ID(); ?>" <?php post_class( 'room-type' ); ?>>
	<div class="hotel-content">
		<div class="hotel-content__main">
			<?php the_title( '<h1 class="awebooking-room-type__title h2">', '</h1>' ); ?>

			<div class="room-type__sections">
				<div class="room-type__section">
					<h4 class="room-type__section-title"><?php esc_html_e( 'Description', 'awebooking' ); ?></h4>

					<div class="room-type__content">
						<?php the_content(); ?>
					</div>
				</div>

				<div class="room-type__section">
					<h4 class="room-type__section-title"><?php esc_html_e( 'Amenities', 'awebooking' ); ?></h4>

					<div id="amenities-<?php echo absint( $room_type->get_id() ); ?>">
						<?php $amenities = wp_get_post_terms( $room_type->get_id(), 'hotel_amenity' ); ?>
						<?php foreach ( $amenities as $amenity ) : ?>
							<div class="room-amenity">
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
							</div>
						<?php endforeach; ?>
					</div>
				</div>

				<div class="room-type__section">
					<h4 class="room-type__section-title"><?php esc_html_e( 'Gallery', 'awebooking' ); ?></h4>
					<?php
					$attachment_ids = $room_type->get( 'gallery_ids' );

					if ( has_post_thumbnail() ) {
						array_unshift( $attachment_ids, get_post_thumbnail_id() );
					}
					?>
					<div>
						<?php
							if ( $attachment_ids && has_post_thumbnail() ) {
							foreach ( $attachment_ids as $attachment_id ) {
								$image = '<div class="item">';
								$image .= wp_get_attachment_image( $attachment_id, 'awebooking_thumbnail' );
								$image .= '</div>';
								echo apply_filters( 'awebooking/single_room_type_image_thumbnail_html', $image ); // WPCS: xss ok.
							}
						}
						?>
					</div>
				</div>
			</div>
		</div><!-- /.hotel-content__main -->

		<aside class="hotel-content__aside">
			<div class="search-rooms__form">
				<?php
				// Print the search form.
				abrs_get_search_form([
					'layout' => 'vertical',
				]);
				?>
			</div>
		</aside><!-- /.hotel-content__aside -->
	</div><!-- /.hotel-content -->
</div><!-- #room-type-<?php the_ID(); ?> -->
<?php do_action( 'awebooking/after_single_room_type' ); ?>