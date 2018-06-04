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

		<div class="checkin-checkout-details-panel">
			<div class="columns">
				<div class="column is-5">
					<div class="check-in-check-out-details">
						<div class="column">
							<div class="column is-12"><span class="title-text" data-bind="text: title">Check-in</span>
							</div>
						</div>
						<div class="column">
							<div class="column is-4"><span class="big-text" data-bind="text: day">10</span></div>
							<div class="column is-8 right-content">
								<div class="column">
									<div class="column is-12"><span class="small-text"
									                             data-bind="text: dayOfWeek">Sunday</span></div>
								</div>
								<div class="column">
									<div class="column is-12"><span data-bind="text: monthYear">Jun 2018</span></div>
								</div>
							</div>
						</div>
						<div class="column">
							<div class="column is-12"><span class="title-text" data-bind="text: time">from 14:00</span>
							</div>
						</div>
					</div>
				</div>
				<div class="column is-1"><i class="fa ficon ficon-edge-arcolumn-right ficon-20"></i></div>
				<div class="column is-5">
					<div class="check-in-check-out-details">
						<div class="column">
							<div class="column is-12"><span class="title-text" data-bind="text: title">Check-out</span>
							</div>
						</div>
						<div class="column">
							<div class="column is-4"><span class="big-text" data-bind="text: day">11</span></div>
							<div class="column is-8 right-content">
								<div class="column">
									<div class="column is-12"><span class="small-text" data-bind="text: dayOfWeek">Monday</span></div>
								</div>
								<div class="column">
									<div class="column is-12"><span data-bind="text: monthYear">Jun 2018</span></div>
								</div>
							</div>
						</div>
						<div class="column">
							<div class="column is-12"><span class="title-text" data-bind="text: time">until 12:00</span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="divider-solid-bottom"></div>
		</div>

		<dl class="dl-horizontal dl-horizontal-left">

			<dt id="roomdetails-room-text" data-bind="text: roomDetails.roomText() + ' :'">Room :</dt>
			<dd data-bind="text: roomDetails.roomTypeListText">2 x Single Bed in 4-Bed Room (Female only)</dd>

			<dd id="roomdetails-change-room">
				<a target="_blank" href="">
					<span>Change room</span> <i class="ficon ficon-16 ficon-arrow-right text ficon-txtdeco-none"></i>
				</a>
			</dd>

			<dt data-bind="text: roomDetails.stayText() + ' :'">Stay :</dt>
			<dd id="occupancyDetails" class="occupancy-details" data-bind="text: roomDetails.occupancy">1 night, 2
				rooms, 8 adults
			</dd>
			<dt id="roomdetails-capacity-text" data-bind="text: roomDetails.capacityText()+ ' :'">Max occupancy :</dt>
			<dd data-bind="text: roomDetails.capacity()">8 adults</dd>
			<dt id="roomdetails-room-size-text" data-bind="text: roomDetails.roomSizeText() + ' :'">Room Size :</dt>
			<dd data-bind="text: roomDetails.roomTypes()[0].size()">25 sq.m.</dd>

			<dt id="roomdetails-policy-text" data-bind="text: roomDetails.policyText()+ ' :'">Policy :</dt>
			<dd data-bind="text: roomDetails.bnplMessage">Nothing to pay until June 5, 2018</dd>
		</dl>

		<div class="rooms rooms--booked">
			<?php foreach ( $room_stays as $room_stay ) : ?>

				<?php abrs_get_template( 'reservation/booked-room.php', compact( 'room_stay' ) ); ?>

			<?php endforeach ?>
		</div>

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
