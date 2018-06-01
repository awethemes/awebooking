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

?>

<div class="reservation reservation--summary">
	<h2 class="reservation__title"><?php esc_html_e( 'Reservation Summary', 'awebooking' ); ?></h2>

	<?php if ( 0 === count( $room_stays ) ) : ?>

		<div><p><?php echo esc_html__( 'No room selected', 'awebooking' ); ?></p></div>

	<?php else : ?>

		<div class="rooms rooms--booked">
			<?php foreach ( $room_stays as $room_stay ) : ?>

				<?php abrs_get_template( 'reservation/booked-room.php', compact( 'room_stay' ) ); ?>

			<?php endforeach ?>
		</div>

		<div class="reservation__totals">
			<table>
				<tbody>
					<tr>
						<td></td>
						<td></td>
					</tr>
				</tbody>
			</table>
		</div>

	<?php endif; ?>

</div><!-- .reservation--summary -->
