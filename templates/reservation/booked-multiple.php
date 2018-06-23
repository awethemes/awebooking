<?php
/**
 * This template show the booked room items.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/reservation/booked-multiple.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/* @var \AweBooking\Reservation\Item $room_stay */

$reservation = abrs_reservation();

$res_request = $reservation->resolve_res_request();

?>

<?php foreach ( $room_stays as $key => $room_stay ) : ?>

	<div class="roomdetails-room">
		<div class="roomdetails-room__content">
			<?php $room_type = $room_stay->model(); ?>

			<a href="<?php echo esc_url( abrs_route( "/reservation/remove/{$room_stay->get_row_id()}" ) ); ?>">
				<span><?php esc_html_e( 'Remove', 'awebooking' ); ?></span>
			</a>

			<dl class="roomdetails-room__list">
				<dt class="roomdetails-room__title"><?php esc_html_e( 'Room', 'awebooking' ); ?></dt>
				<dd class="roomdetails-room__text"><?php echo esc_html( $room_stay->get( 'name' ) ); ?></dd>

				<dt class="roomdetails-room__title"><?php esc_html_e( 'Stay', 'awebooking' ); ?></dt>
				<dd class="roomdetails-room__text" class="occupancy-details">
					<?php
					/* translators: %1$s nights, %2$s guest */
					printf( esc_html_x( '%1$s, %2$s', 'room stay', 'awebooking' ),
						abrs_format_night_counts( $res_request['nights'] ),
						abrs_format_guest_counts( $res_request->get_guest_counts() )
					); // WPCS: xss ok.
					?>
				</dd>

				<dt class="roomdetails-room__title"><?php esc_html_e( 'Max occupancy', 'awebooking' ); ?></dt>
				<dd class="roomdetails-room__text">
					<?php
					dump( $room_stay->get_quantity() );

					/* translators: %s max occupancy */
					printf( esc_html_x( '%s people', 'max occupancy', 'awebooking' ), absint( $room_type->get( 'maximum_occupancy' ) ) );
					?>
				</dd>
			</dl>
		</div>

		<div class="roomdetails-price">
			<dl>
				<dt>
					<?php
						/* translators: %1$s quantity, %2$s nights */
						printf( esc_html__( 'Price (%1$s x %2$s)', 'awebooking' ),
							sprintf(
								'%1$d %2$s',
								esc_html( $room_stay->get( 'quantity' ) ),
								esc_html( _n( 'room', 'rooms', $room_stay->get( 'quantity' ), 'awebooking' ) )
							),
							abrs_format_night_counts( $res_request['nights'] )
						); // WPCS: xss ok.
					?>
				</dt>
				<dd><?php abrs_price( $room_stay->get_total_price_exc_tax() ); ?></dd>

				<dt>VAT</dt>
				<dd class="roomdetails-price__vat">free</dd>

				<dt><?php esc_html_e( 'Subtotal', 'awebooking' ); ?></dt>
				<dd><?php abrs_price( $room_stay->get_total_price_exc_tax() ); ?></dd>
			</dl>
		</div>
	</div>

<?php endforeach; ?>
