<?php
/**
 * The template displaying the reservation stay.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/reservation/dates.php.
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

?>

<div class="roomdetails-price roomdetails-total">
	<dl>
		<dt><?php esc_html_e( 'Total', 'awebooking' ); ?></dt>
		<dd><?php abrs_price( $reservation->get_total() ); ?></dd>
	</dl>

	<p class="roomdetails-total__info">
		<strong>Giá đã bao gồm:</strong>
		Phí dịch vụ 5%, Thuế 10%
	</p>
</div>
