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

?>


<dl class="roomdetails-room">
	<dt class="roomdetails-room__title">Room :</dt>
	<dd class="roomdetails-room__text">2 x Single Bed in 4-Bed Room (Female only)</dd>

	<dt class="roomdetails-room__title">Stay :</dt>
	<dd class="roomdetails-room__text" class="occupancy-details">1 night, 2
		rooms, 8 adults
	</dd>

	<dt class="roomdetails-room__title">Max occupancy :</dt>
	<dd class="roomdetails-room__text">8 adults</dd>
</dl>

<div class="roomdetails-price">
	<dl class="roomdetails-price-base">
		<dt>Price (1 room x 1 night)</dt>
		<dd>700.814</dd>

		<dt>VAT</dt>
		<dd class="roomdetails-price-base__text roomdetails-price-vat">free</dd>
	</dl>

	<div class="roomdetails-price-footer">
		<dl class="roomdetails-price-total">
			<dt data-bind="text: roomDetails.roomText() + ' :'">Price</dt>
			<dd data-bind="text: roomDetails.roomTypeListText">1.700.814</dd>
		</dl>

		<p class="roomdetails-price-footer__info">
			<strong>Giá đã bao gồm:</strong>
			Phí dịch vụ 5%, Thuế 10%
		</p>
	</div>
<div>
