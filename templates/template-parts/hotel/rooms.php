<?php
/**
 * The template for displaying hotel rooms in the template-parts/hotel/content.php template
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/template-parts/hotel/rooms.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$rooms = abrs_get_room_types_by_hotel( get_the_ID() );

if ( ! $rooms->have_posts() ) {
	return;
}
?>

<div class="hotel__section hotel-rooms-section">
	<h4 class="hotel__section-title"><?php esc_html_e( 'Rooms', 'awebooking' ); ?></h4>

	<div class="hotel__rooms">
		<?php while ( $rooms->have_posts() ) : $rooms->the_post(); ?>
			<?php abrs_get_template_part( 'template-parts/archive/content', apply_filters( 'abrs_archive_room_layout', '' ) ); ?>
		<?php endwhile; ?>
	</div>
</div>
<?php wp_reset_postdata(); ?>
