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

$room_stay = $room_stays->first();

$room_type = $room_stay->model();
$res_request = $room_stay->data->request;
?>

<div class="roomdetails-room">
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
</div>


<div class="roomdetails-price roomdetails-total">
	<dl>
		<dt>Price</dt>
		<dd>1.700.814</dd>
	</dl>

	<p class="roomdetails-total__info">
		<strong>Giá đã bao gồm:</strong>
		Phí dịch vụ 5%, Thuế 10%
	</p>
</div>
