<?php
/**
 * This template show the search result item.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/search/result-item.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* @var \AweBooking\Model\Room_Type $room_type */
/* @var \AweBooking\Availability\Room_Rate $room_rate */

$remain_rooms = $room_rate->get_remain_rooms();
$rate_plan = $room_rate->get_rate_plan();

?>

<div class="roommaster">
	<div class="roommaster-header">
		<?php do_action( 'abrs_search_result_header', $room_type, $room_rate ); ?>
	</div>

	<div class="roommaster-wrapper">
		<div class="roommaster-content">

			<div class="columns">
				<div class="column-lg-3">
					<?php do_action( 'abrs_search_result_room_type', $room_type, $room_rate ); ?>
				</div>

				<div class="column-lg-9">
					<div class="roommaster-list">
					<div class="columns no-gutters roommaster-list__header">
						<div class="column-lg-4">
							<h4 class="roommaster-content__title"><?php esc_html_e( 'Choose your deal', 'awebooking' ); ?></h4>
						</div>

						<div class="column-lg-2">
							<h4 class="roommaster-content__title"><?php esc_html_e( 'Capacity', 'awebooking' ); ?></h4>
						</div>

						<div class="column-lg-3">
							<h4 class="roommaster-content__title"><?php esc_html_e( 'Price', 'awebooking' ); ?></h4>
						</div>

						<div class="column-lg-3"></div>
					</div>

					<div class="columns no-gutters roommaster-list__content">
						<div class="column-lg-4">
							<?php abrs_get_template( 'search/result/deal.php', compact( 'room_type', 'room_rate' ) ); ?>
						</div>

						<div class="column-lg-2">
							<?php abrs_get_template( 'search/result/occupancy.php', compact( 'room_type', 'room_rate' ) ); ?>
						</div>

						<div class="column-lg-3">
							<?php do_action( 'abrs_search_result_room_price', $room_type, $room_rate ); ?>
						</div>

						<div class="column-lg-3">
							<?php abrs_get_template( 'search/result/button.php', compact( 'room_type', 'room_rate' ) ); ?>
						</div>
					</div>
				</div>
				</div>
			</div>

		</div>
	</div>
</div>
