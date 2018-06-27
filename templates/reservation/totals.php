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

<div class="reservation__section reservation__section--totals">
	<dl>
		<dt><?php esc_html_e( 'Total', 'awebooking' ); ?></dt>
		<dd><?php abrs_price( $reservation->get_total() ); ?></dd>
	</dl>
</div>
