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

?>

<?php foreach ( $room_stays as $key => $room_stay ) : ?>
	<?php
		$room_type = $room_stay->model();
		$res_request = $room_stay->data->request;
	?>

	<a href="<?php echo esc_url( abrs_route( "/reservation/remove/{$room_stay->get_row_id()}" ) ); ?>">
		<span><?php echo esc_html__( 'Remove', 'awebooking' ) ?></span>
	</a>

	<dl class="roomdetails-room">
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
				/* translators: %s max occupancy */
				printf( esc_html_x( '%s people', 'max occupancy', 'awebooking' ), absint( $room_type->get( 'maximum_occupancy' ) ) );
			?>
		</dd>
	</dl>

	<div class="roomdetails-price">
		<dl class="roomdetails-price-base">
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
			<dd><?php echo abrs_price( $room_stay->get_total_price_exc_tax() ); // WPCS: xss ok. ?></dd>

			<dt>VAT</dt>
			<dd class="roomdetails-price-base__text roomdetails-tax">free</dd>

			<dt>Subtotal</dt>
			<dd class="roomdetails-price-base__text roomdetails-subtotal">
				<?php echo abrs_price( $room_stay->get_total_price() ); // WPCS: xss ok. ?>
			</dd>
		</dl>
	</div>

<?php endforeach; ?>

<div class="roomdetails-price">

	<div class="roomdetails-price-footer">
		<dl class="roomdetails-price-total">
			<dt><?php esc_html_e( 'Total', 'awebooking' ); ?></dt>
			<dd><?php abrs_price( $reservation->get_total() ) ?></dd>
		</dl>

		<p class="roomdetails-price-footer__info">
			<strong>Giá đã bao gồm:</strong>
			Phí dịch vụ 5%, Thuế 10%
		</p>
	</div>
</div>
