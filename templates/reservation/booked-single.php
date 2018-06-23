<?php
/**
 * This template show the booked room item.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/reservation/booked-single.php.
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

/* @var \AweBooking\Model\Room_Type $room_type */
$room_type = $room_stay->model();

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

			<dt><?php esc_html_e( 'Max occupancy', 'awebooking' ); ?></dt>
			<dd>
				<?php
				/* translators: %s max occupancy */
				printf( esc_html_x( '%s people', 'max occupancy', 'awebooking' ), absint( $room_type->get( 'maximum_occupancy' ) ) );
				?>
			</dd>
		</dl>

		<div class="roomdetails-price">
			<dl>
				<dt>Price (1 room x 1 night)</dt>
				<dd>700.814</dd>

				<dt>VAT</dt>
				<dd>free</dd>
			</dl>
		</div>

	</div><!-- /.roomdetails-room -->
</div><!-- .reservation__section -->
