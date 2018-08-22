<?php
/**
 * The template for displaying room information in the template-parts/archive/content.php template
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/template-parts/archive/information.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room_type;

?>

<ul id="list-room__info-list-<?php echo absint( $room_type->get_id() ); ?>" class="list-room__info-list">
	<?php if ( $room_type->get( 'view' ) ) : ?>
		<li class="info-item">
			<span class="info-icon">
				<i class="aficon aficon-business"></i>
				<span class="screen-reader-text"><?php echo esc_html_x( 'Room view', 'room view button', 'awebooking' ); ?></span>
			</span>
			<?php echo esc_html( $room_type->get( 'view' ) ); ?>
		</li>
	<?php endif; ?>

	<?php if ( $room_type->get( 'area_size' ) ) : ?>
		<li class="info-item">
			<span class="info-icon">
				<i class="aficon aficon-sqm"></i>
				<span class="screen-reader-text"><?php echo esc_html_x( 'Area size', 'area size button', 'awebooking' ); ?></span>
			</span>
			<?php
				/* translators: %1$s area size, %2$s measure unit */
				printf( esc_html_x( '%1$s %2$s', 'room area size', 'awebooking' ),
					esc_html( $room_type->get( 'area_size' ) ),
					abrs_get_measure_unit_label()
				); // WPCS: xss ok.
			?>
		</li>
	<?php endif; ?>

	<?php if ( $room_type->get( 'beds' ) ) : ?>
		<li class="info-item">
			<span class="info-icon">
				<i class="aficon aficon-bed"></i>
				<span class="screen-reader-text"><?php echo esc_html_x( 'Bed', 'bed button', 'awebooking' ); ?></span>
			</span>
			<?php print abrs_get_room_beds( $room_type ); // WPCS: xss ok. ?>
		</li>
	<?php endif; ?>

	<?php do_action( 'abrs_after_archive_room_informations' ); ?>
</ul><!-- #list-room__info-list-<?php the_ID(); ?> -->
