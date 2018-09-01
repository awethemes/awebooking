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
					<?php do_action( 'abrs_search_result_room_list', $room_type, $room_rate ); ?>
				</div>
			</div>
		</div>

	</div>
</div>
