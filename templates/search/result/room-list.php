<?php
/**
 * This template show the search result room list.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/search/result/room-list.php.
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

?>

<div class="roommaster-list">
	<div class="columns no-gutters">
		<div class="column-4">
			<div class="roommaster-content__title"><?php esc_html_e( 'Choose your deal', 'awebooking' ); ?></div>
		</div>
		<div class="column-2">
			<div class="roommaster-content__title"><?php esc_html_e( 'Capacity', 'awebooking' ); ?></div>
		</div>
		<div class="column-3">
			<div class="roommaster-content__title"><?php esc_html_e( 'Price', 'awebooking' ); ?></div>
		</div>
		<div class="column-3"></div>
	</div>

	<?php //foreach ($variable as $key => $value) : ?>
	<div class="columns no-gutters">
		<div class="column-4">
			<?php abrs_get_template( 'search/result/deal.php', compact( 'room_type', 'room_rate' ) ); ?>
		</div>
		<div class="column-2">
			<?php abrs_get_template( 'search/result/capacity.php', compact( 'room_type', 'room_rate' ) ); ?>
		</div>
		<div class="column-3">
			<?php do_action( 'abrs_after_result_item_price', $room_type, $room_rate ); ?>
		</div>
		<div class="column-3">
			<?php abrs_get_template( 'search/result/button.php', compact( 'room_type', 'room_rate' ) ); ?>
		</div>
	</div>
	<?php //endforeach; ?>
</div>
