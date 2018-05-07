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

if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: xss ok.
	return;
}
?>
<div id="room-type-<?php the_ID(); ?>" <?php post_class( 'awebooking-room-type' ); ?>>
	<div class="awebooking-room-type__wrapper">

		<div class="awebooking-room-type__header">
			<?php
				awebooking_template_single_title();
				awebooking_template_single_price();
			?>
		</div>

		<div class="awebooking-room-type__media">

			<?php
				/**
				 * awebooking/before_single_room_type_summary hook.
				 *
				 * @hooked awebooking_show_room_type_images - 20
				 */
				do_action( 'awebooking/before_single_room_type_summary' );
			?>
		</div>

		<div class="awebooking-room-type__check-form summary entry-summary">

			<?php
				/**
				 * awebooking/single_room_type_summary hook.
				 *
				 * @hooked awebooking_template_single_title - 5
				 * @hooked awebooking_template_single_price - 10
				 * @hooked awebooking_template_single_form - 15
				 */
				do_action( 'awebooking/single_room_type_summary' );
			?>

		</div><!-- .summary -->

	</div>

	<div class="awebooking-room-type__sections">
		<div class="awebooking-sections">
			<div class="awebooking-section">
				<h4><?php esc_html_e( 'Description', 'awebooking' ); ?></h4>

				<div class="awebooking-section__content entry-content">
					<?php the_content(); ?>
				</div>
			</div>

			<div class="awebooking-section">
				<h4><?php esc_html_e( 'Amenities', 'awebooking' ); ?></h4>

				<div class="awebooking-section__content entry-content">
					<?php $amenities = wp_get_post_terms( get_the_ID(), 'hotel_amenity' ); ?>
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
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>

</div><!-- #room-type-<?php the_ID(); ?> -->

<?php do_action( 'awebooking/after_single_room_type' ); ?>
