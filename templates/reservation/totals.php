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
		<dt><?php esc_html_e( 'Subtotal', 'awebooking' ); ?></dt>
		<dd><?php abrs_price( $reservation->get_subtotal() ); ?></dd>
	</dl>

	<?php do_action( 'abrs_reservation_before_total' ); ?>

	<dl>
		<dt><strong><?php esc_html_e( 'Total', 'awebooking' ); ?></strong></dt>
		<dd><?php abrs_price( $reservation->get_total() ); ?></dd>
	</dl>

	<?php do_action( 'abrs_reservation_after_total' ); ?>
</div>
