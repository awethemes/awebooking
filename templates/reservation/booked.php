<?php
/**
 * This template show the booked rooms.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/reservation/booked.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

$room_stays = abrs_reservation()->get_room_stays();

?>

<div class="box reservation reservation--summary">
	<h2 class="reservation__title"><?php esc_html_e( 'Reservation Summary', 'awebooking' ); ?></h2>

	<?php if ( 0 === count( $room_stays ) ) : ?>

		<div><p><?php echo esc_html__( 'No room selected', 'awebooking' ); ?></p></div>

	<?php else : ?>

		<div class="checkin-checkout-details">
			<div class="checkin-checkout-details___item tb-width-40">
				<span class="checkin-checkout-details__subtitle" data-bind="text: title">Check-in</span>
				<div class="checkin-checkout-details__info">
					<span class="meta-day">10</span>
					<span class="meta-day-week" data-bind="text: dayOfWeek">Sunday</span>
					<span class="meta-month-year">Jun 2018</span>
				</div>
				<div class="checkin-checkout-details__from">
					<span class="meta-time">from 14:00</span>
				</div>
			</div>
			<div class="checkin-checkout-details___item tb-width-20"><i class="fa ficon ficon-edge-arcolumn-right ficon-20"></i></div>
			<div class="checkin-checkout-details___item tb-width-40">
				<span class="checkin-checkout-details__subtitle" data-bind="text: title">Check-out</span>
				<div class="checkin-checkout-details__info">
					<span class="meta-day">11</span>
					<span class="meta-day-week" data-bind="text: dayOfWeek">Sunday</span>
					<span class="meta-month-year">Jun 2018</span>
				</div>
				<div class="checkin-checkout-details__from">
					<span class="meta-time">to 14:00</span>
				</div>
			</div>
		</div>
		<div class="divider-solid-bottom"></div>
		
		<dl class="roomdetails-room">
			<dt class="roomdetails-room__title" data-bind="text: roomDetails.roomText() + ' :'">Room :</dt>
			<dd class="roomdetails-room__text" data-bind="text: roomDetails.roomTypeListText">2 x Single Bed in 4-Bed Room (Female only)</dd>
			<dd class="roomdetails-room__text" data-bind="text: roomDetails.roomTypeListText">2 x Single Bed in 4-Bed Room (Female only)</dd>

			<dd class="roomdetails-room__text">
				<a target="_blank" href="" class="roomdetails-change-room">
					<span>Change room</span> <i class="ficon ficon-16 ficon-arrow-right text ficon-txtdeco-none"></i>
				</a>
			</dd>

			<dt class="roomdetails-room__title" data-bind="text: roomDetails.stayText() + ' :'">Stay :</dt>
			<dd class="roomdetails-room__text" class="occupancy-details" data-bind="text: roomDetails.occupancy">1 night, 2
				rooms, 8 adults
			</dd>

			<dt class="roomdetails-room__title" data-bind="text: roomDetails.capacityText()+ ' :'">Max occupancy :</dt>
			<dd class="roomdetails-room__text" data-bind="text: roomDetails.capacity()">8 adults</dd>

			<dt class="roomdetails-room__title" data-bind="text: roomDetails.roomSizeText() + ' :'">Room Size :</dt>
			<dd class="roomdetails-room__text" data-bind="text: roomDetails.roomTypes()[0].size()">25 sq.m.</dd>

			<dt class="roomdetails-room__title" data-bind="text: roomDetails.policyText()+ ' :'">Policy :</dt>
			<dd class="roomdetails-room__text" data-bind="text: roomDetails.bnplMessage">Nothing to pay until June 5, 2018</dd>
		</dl>

		<div class="rooms rooms--booked">
			<?php foreach ( $room_stays as $room_stay ) : ?>

				<?php abrs_get_template( 'reservation/booked-room.php', compact( 'room_stay' ) ); ?>

			<?php endforeach ?>
		</div>

		<div class="roomdetails-price">
			<dl class="roomdetails-price-base">
				<dt data-bind="text: roomDetails.roomText() + ' :'">Old price (1 room x 1 night)</dt>
				<dd data-bind="text: roomDetails.roomTypeListText">
					<del>1.700.814</del>
				</dd>
				<dt data-bind="text: roomDetails.roomText() + ' :'">Price (1 room x 1 night)</dt>
				<dd data-bind="text: roomDetails.roomTypeListText">700.814</dd>
				<dt data-bind="text: roomDetails.roomText() + ' :'">Vat</dt>
				<dd class="roomdetails-price-base__text roomdetails-price-vat" data-bind="text: roomDetails.roomTypeListText">free</dd>
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
				<p class="roomdetails-price-footer__info-more">
					<small> Lựa chọn khôn khéo! Bạn tiết kiệm được 1.890.235 ₫</small>
				</p>
			</div>
			
		<div>

		<div class="reservation__totals">
			<table>
				<tbody>
					<tr>
						<td></td>
						<td></td>
					</tr>
				</tbody>
			</table>
		</div>

	<?php endif; ?>

</div><!-- .reservation--summary -->
