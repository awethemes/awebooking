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
	</form>

	<?php awebooking_get_template( 'search/booked.php', compact( 'guest', 'reservation' ) ); ?>
</div>
