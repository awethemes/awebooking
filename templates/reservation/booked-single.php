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

$room_stay = $room_stays->first();
$room_type = $room_stay->model();

$check_in  = abrs_date( $room_stay->get( 'check_in' ) );
$check_out = abrs_date( $room_stay->get( 'check_out' ) );

?><div class="reservation_details">
	<div class="reservation_details___item tb-width-40">
		<span class="reservation_details__subtitle"><?php echo esc_html__( 'Check-in', 'awebooking' ); ?></span>

		<div class="reservation_details__info">
			<span class="meta-day"><?php echo esc_html( $check_in->date_i18n( 'd' ) ); ?></span>
			<span class="meta-day-week"><?php echo esc_html( $check_in->date_i18n( 'l' ) ); ?></span>
			<span class="meta-month-year"><?php echo esc_html( $check_in->date_i18n( 'M Y' ) ); ?></span>
		</div>

		<div class="reservation_details__from">
			<span class="meta-time">from 14:00</span>
		</div>
	</div>

	<div class="reservation_details___item tb-width-20">
		<i class="fa ficon ficon-edge-arcolumn-right ficon-20"></i>
	</div>

	<div class="reservation_details___item tb-width-40">
		<span class="reservation_details__subtitle"><?php echo esc_html__( 'Check-out', 'awebooking' ); ?></span>

		<div class="reservation_details__info">
			<span class="meta-day"><?php echo esc_html( $check_out->date_i18n( 'd' ) ); ?></span>
			<span class="meta-day-week"><?php echo esc_html( $check_out->date_i18n( 'l' ) ); ?></span>
			<span class="meta-month-year"><?php echo esc_html( $check_out->date_i18n( 'M Y' ) ); ?></span>
		</div>

		<div class="reservation_details__from">
			<span class="meta-time">to 14:00</span>
		</div>
	</div>
</div>

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
