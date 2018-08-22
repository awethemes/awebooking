<?php
/**
 * This template show the search result room type.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/search/result/room-type.php.
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

<div class="roommaster-info">
	<div class="roommaster-info__image">
		<?php
		if ( has_post_thumbnail( $room_type->get_id() ) ) {
			echo get_the_post_thumbnail( $room_type->get_id(), 'awebooking_archive' );
		}
		?>
	</div>

	<ul class="roommaster-info__list">
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

		<?php do_action( 'abrs_after_search_result_room_type_informations' ); ?>
	</ul>
</div>
