<?php
/**
 * Display the details in a booking.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/emails/partials/line-booking.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><table class="table-booking" width="100%" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th>Arrival - Departure</th>
			<th>Nights</th>
			<th>Adults</th>
			<th>Children</th>
			<th>Infants</th>
			<th>Total</th>
		</tr>
	</thead>
	<tbody>
		<tr>

		</tr>
	</tbody>
</table>

<table class="table-booking-totals">
	<tbody>
		<tr>
			<th>Total:</th>
			<td></td>
		</tr>

		<tr>
			<th>Paid:</th>
			<td></td>
		</tr>

		<tr>
			<th>Balance Due:</th>
			<td></td>
		</tr>
	</tbody>
</table>
