<?php
/**
 * This template show the booked room item.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/reservation/selected-room.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$reservation = abrs_reservation();
$res_request = $reservation->resolve_res_request();

/* @var \AweBooking\Reservation\Item $room_stay */
$room_stay = $reservation->get_room_stays()->first();

?>

<div class="reservation__section reservation__section--room">
	<div class="roomdetails-room">

		<dl class="roomdetails-room__list">
			<dt><?php esc_html_e( 'Room', 'awebooking' ); ?></dt>
			<dd><?php echo esc_html( $room_stay->get( 'name' ) ); ?></dd>

			<dt><?php esc_html_e( 'Stay', 'awebooking' ); ?></dt>
			<dd>
				<?php
				/* translators: %1$s nights, %2$s guest */
				printf( esc_html_x( '%1$s, %2$s', 'room stay', 'awebooking' ),
					abrs_format_night_counts( $res_request['nights'] ),
					abrs_format_guest_counts( $res_request->get_guest_counts() )
				); // WPCS: XSS OK.
				?>
			</dd>

			<dt><?php esc_html_e( 'Price', 'awebooking' ); ?></dt>
			<dd><?php abrs_price( $room_stay->get_price() ); ?></dd>

			<?php if ( abrs_tax_enabled() && $room_stay->get_tax() > 0 ) : ?>
				<dt><?php echo isset( $room_stay['tax_rate']['name'] ) ? esc_html( $room_stay['tax_rate']['name'] ) : esc_html__( 'Tax', 'awebooking' ); ?></dt>
				<dd><?php abrs_price( $room_stay->get_total_tax() ); ?></dd>
			<?php endif; ?>
		</dl>

	</div><!-- /.roomdetails-room -->
</div><!-- .reservation__section -->
