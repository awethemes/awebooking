<?php
/**
 * This template show the booked rooms.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/reservation/booked.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

$room_stays = abrs_reservation()->get_room_stays();

?><div class="reservation reservation--summary">
	<h2 class="reservation__title"><?php esc_html_e( 'Reservation Summary', 'awebooking' ); ?></h2>

	<?php if ( 0 === count( $room_stays ) ) : ?>

		<div class="reservation__empty"><?php echo esc_html__( 'No rooms selected', 'awebooking' ); ?></div>

	<?php else : ?>

		<?php abrs_get_template( 'reservation/stay.php' ); ?>

		<?php if ( 'single_room' === abrs_get_reservation_mode() ) : ?>
			<?php abrs_get_template( 'reservation/booked-single.php', compact( 'room_stays' ) ); ?>
		<?php else : ?>
			<?php abrs_get_template( 'reservation/booked-multiple.php', compact( 'room_stays' ) ); ?>
		<?php endif; ?>

	<?php endif; ?>

</div><!-- .reservation--summary -->
