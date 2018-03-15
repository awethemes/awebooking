<?php
/**
 * The template for displaying check availability results.
 *
 * @author  Awethemes
 * @package AweBooking/Templates
 * @version 3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/* @vars $guest, $reservation, $results */

list( $guest, $timespan ) = [ $reservation->get_guest(), $reservation->get_timespan() ];

do_action( 'awebooking/template_notices' );

?>

<div class="awebooking-availability-container has-sidebar">
	<form method="POST" action="<?php echo esc_url( awebooking( 'url' )->route( 'reservation' ) ); ?>">
		<?php wp_nonce_field( 'awebooking_reservation' ); ?>

		<ul class="room_types awebooking-availability-room-types">
			<?php if ( $results->isEmpty() ) : ?>

				<?php awebooking_get_template( 'search/no-results.php', compact( 'guest', 'reservation' ) ); ?>

			<?php else : ?>

				<?php foreach ( $results as $availability ) : ?>
					<li class="awebooking-availability-room-type">

						<?php awebooking_get_template( 'search/result-item.php', compact( 'availability', 'guest', 'reservation' ) ); ?>

					</li><!-- /.awebooking-availability-room-type -->
				<?php endforeach; ?>

			<?php endif; ?>
		</ul><!-- /.awebooking-availability-room-types -->

		<?php if ( $session_id = $reservation->get_session_id() ) : ?>
			<input type="hidden" name="session_id" value="<?php echo esc_attr( $session_id ); ?>">
		<?php endif; ?>

		<input type="hidden" name="check_in" value="<?php echo esc_attr( $timespan->get_start_date()->toDateString() ); ?>">
		<input type="hidden" name="check_out" value="<?php echo esc_attr( $timespan->get_end_date()->toDateString() ); ?>">

		<input type="hidden" name="adults" value="<?php echo esc_attr( $guest->get_adults() ); ?>">
		<?php if ( awebooking( 'setting' )->is_children_bookable() ) : ?>
			<input type="hidden" name="children" value="<?php echo esc_attr( $guest->get_children() ); ?>">
		<?php endif; ?>

		<?php if ( awebooking( 'setting' )->is_infants_bookable() ) : ?>
			<input type="hidden" name="infants" value="<?php echo esc_attr( $guest->get_infants() ); ?>">
		<?php endif; ?>

		<?php do_action( 'awebooking/after_search_form', $reservation ); ?>
	</form>

	<?php awebooking_get_template( 'search/booked.php', compact( 'guest', 'reservation' ) ); ?>
</div>
