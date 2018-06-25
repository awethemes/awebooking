<?php
/**
 * This template displaying the reservation details.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/reservation/reservation.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

$room_stays = abrs_reservation()->get_room_stays();

?><div class="reservation">
	<h2 class="reservation__title"><?php esc_html_e( 'Reservation Summary', 'awebooking' ); ?></h2>

	<?php if ( 0 === count( $room_stays ) ) : ?>

		<div class="reservation__empty">
			<span><i class="aficon aficon-bed"></i></span>
			<p><?php echo esc_html__( 'No rooms selected', 'awebooking' ); ?></p>
		</div>

	<?php else : ?>

		<?php abrs_get_template( 'reservation/dates.php' ); ?>

		<?php if ( 'single_room' === abrs_get_reservation_mode() ) : ?>
			<?php abrs_get_template( 'reservation/selected-room.php', compact( 'room_stays' ) ); ?>
		<?php else : ?>
			<?php abrs_get_template( 'reservation/selected-rooms.php', compact( 'room_stays' ) ); ?>
		<?php endif; ?>

		<?php abrs_get_template( 'reservation/totals.php' ); ?>

	<?php endif; ?>

</div><!-- .reservation -->
