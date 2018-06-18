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

do_action( 'abrs_print_notices' );

if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}

?>

<div id="room-<?php the_ID(); ?>" <?php post_class( 'room-type' ); ?>>

	<div class="columns">
		<div class="room-type__media column-8">
		</div>

		<div class="room-type__check-form column-4">
			<?php the_title( '<h1 class="awebooking-room-type__title h2">', '</h1>' ); ?>
		</div><!-- .summary -->
	</div>

	<div class="room-type__sections">
		<div class="room-type__section">
			<h4><?php esc_html_e( 'Description', 'awebooking' ); ?></h4>

			<div class="room-type__content">
				<?php the_content(); ?>
			</div>
		</div>

		<div class="room-type__section">
			<h4><?php esc_html_e( 'Amenities', 'awebooking' ); ?></h4>

			<ul class="amenities">
				<?php $amenities = wp_get_post_terms( get_the_ID(), 'hotel_amenity' ); ?>
				<?php foreach ( $amenities as $amenity ) : ?>
					<?php $icon = get_term_meta( $amenity->term_id, '_icon', true ); ?>
					<li class="amenity <?php echo $icon ? 'has-icon' : ''; ?>">
						<?php if ( $icon ) : ?>
							<span class="amenity__icon">
								<?php if ( 'svg' === $icon['type'] || 'image' === $icon['type'] ) : ?>
									<?php echo wp_get_attachment_image( $icon['icon'] ); ?>
								<?php else : ?>
									<i class="<?php echo esc_attr( $icon['type'] . ' ' . $icon['icon'] ); ?>"></i>
								<?php endif; ?>
							</span>
						<?php endif; ?>
						<h3 class="amenity__title"><?php echo esc_html( $amenity->name ); ?></h3>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>

</div><!-- #room-type-<?php the_ID(); ?> -->

<?php do_action( 'awebooking/after_single_room_type' ); ?>
