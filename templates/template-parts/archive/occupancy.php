<?php
/**
 * The template for displaying room occupancy in the template-parts/archive/content.php template
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/template-parts/archive/occupancy.php.
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

<ul id="list-room__occupancy-<?php echo absint( $room_type->get_id() ); ?>" class="list-room__occupancy">
	<?php if ( $room_type->get( 'number_adults' ) ) : ?>
		<li class="list-room-occupancy__item">
			<?php
				/* translators: %1$s number adults, %2$s adult button */
				printf( esc_html_x( '%1$s x %2$s', 'number adults', 'awebooking' ),
					absint( $room_type->get( 'number_adults' ) ),
					'<i class="aficon aficon-man"></i><span class="screen-reader-text">' . esc_html_x( 'Adult', 'adult button', 'awebooking' ) . '</span>'
				);
			?>
		</li>
	<?php endif; ?>

	<?php if ( $room_type->get( 'number_children' ) ) : ?>
		<li class="list-room-occupancy__item">
			<?php
				/* translators: %1$s number children, %2$s child button */
				printf( esc_html_x( '%1$s x %2$s', 'number children', 'awebooking' ),
					absint( $room_type->get( 'number_children' ) ),
					'<i class="aficon aficon-body"></i><span class="screen-reader-text">' . esc_html_x( 'Child', 'child button', 'awebooking' ) . '</span>'
				);
			?>

		</li>
	<?php endif; ?>

	<?php if ( $room_type->get( 'number_infants' ) ) : ?>
		<li class="list-room-occupancy__item">
			<?php
				/* translators: %1$s number infants, %2$s infant button */
				printf( esc_html_x( '%1$s x %2$s', 'number infants', 'awebooking' ),
					absint( $room_type->get( 'number_infants' ) ),
					'<i class="aficon aficon-baby"></i><span class="screen-reader-text">' . esc_html_x( 'Infant', 'infant button', 'awebooking' ) . '</span>'
				);
			?>
		</li>
	<?php endif; ?>
</ul><!-- #list-room__occupancy-<?php the_ID(); ?> -->
