<?php
/**
 * Display the hotel details in a booking.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/emails/partials/line-hotel.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hotel = abrs_get_hotel( $booking->get( 'hotel_id' ) );

if ( is_null( $hotel ) ) {
	$hotel = abrs_get_primary_hotel();
}

?><table class="table-hotel" width="100%" cellpadding="0" cellspacing="0">
	<tbody>
		<tr>
			<td class="align-left">
				<strong><?php esc_html_e( 'RESERVATION ID', 'awebooking' ); ?></strong><br>
				<strong class="booking-id">
					<?php if ( $email->is_customer_email() ) : ?>
						<span>#<?php echo esc_html( $booking->get_booking_number() ); ?></span>
					<?php else: ?>
						<a href="<?php echo esc_url( get_edit_post_link( $booking->get_id() ) ); ?>" target="_blank">#<?php echo esc_html( $booking->get_booking_number() ); ?></a>
					<?php endif; ?>
				</strong>
			</td>

			<td class="align-right">
				<div class="line-hotel-address">
					<strong><?php echo esc_html( $hotel->get( 'name' ) ); ?></strong><br>
					<?php echo esc_html( $hotel->get( 'hotel_address' ) ); ?><br>
					<?php echo abrs_make_phone_clickable( $hotel->get( 'hotel_phone' ) ); // WPCS: XSS OK. ?><br>
				</div>
			</td>
		</tr>
	</tbody>
</table>
