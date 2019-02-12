<?php
/**
 * This template show the booked room item.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/reservation/selected-room.php.
 *
 * @var \AweBooking\Reservation\Item $room_stay
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/* @var $res_request \AweBooking\Availability\Request|mixed */
$res_request = abrs_optional( $room_stay->data() )
	->get_request();

if ( empty( $res_request ) ) {
	return;
}

?>

<div class="roomdetails-room">
	<dl class="roomdetails-room__list">
		<dt><?php esc_html_e( 'Room', 'awebooking' ); ?></dt>
		<dd><?php echo esc_html( $room_stay->get( 'name' ) ); ?></dd>

		<dt><?php esc_html_e( 'Stay', 'awebooking' ); ?></dt>
		<dd>
			<?php
			/* translators: %1$s nights, %2$s guest */
			printf( esc_html_x( '%1$s, %2$s', 'room stay', 'awebooking' ),
				abrs_ngettext_nights( $res_request['nights'] ),
				abrs_format_guest_counts( $res_request->get_guest_counts() )
			); // WPCS: XSS OK.
			?>
		</dd>
	</dl>

	<dl class="roomdetails-room__list--price">
		<dt>
			<?php esc_html_e( 'Price', 'awebooking' ); ?>
			<small>
				<?php
				/* translators: %1$s rooms, %2$s nights */
				printf( _x( '(%1$s x %2$s)', 'price details rooms x nights', 'awebooking' ),
					abrs_ngettext_nights( $res_request->get( 'nights' ) ),
					abrs_ngettext_rooms( $room_stay->get_quantity() )
				); // WPCS: XSS OK.
				?>
			</small>
		</dt>
		<dd><?php abrs_price( $room_stay->get_subtotal() ); ?></dd>

		<?php if ( abrs_tax_enabled() && $room_stay->get_tax() > 0 ) : ?>
			<dt><?php echo isset( $room_stay['tax_rates']['name'] ) ? esc_html( $room_stay['tax_rate']['name'] ) : esc_html__( 'Tax', 'awebooking' ); ?></dt>
			<dd><?php abrs_price( $room_stay->get_total_tax() ); ?></dd>
		<?php endif; ?>
	</dl>

	<div class="roomdetails-room__actions">
		<?php if ( ! abrs_is_checkout_page() ) : ?>
			<a href="<?php echo esc_url( abrs_route( "reservation/remove/{$room_stay->get_row_id()}" ) ); ?>" class="remove-selected-room"><?php esc_html_e( 'Remove', 'awebooking' ); ?></a>
		<?php endif; ?>
	</div>
</div><!-- /.roomdetails-room -->
