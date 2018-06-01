<?php
/**
 * This template show the booked room item.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/reservation/booked-room.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

$room_type = abrs_get_room_type( $room_stay->room_type );
$rate_plan = abrs_get_rate_plan( $room_stay->rate_plan );

?>

<div class="mini-room mini-room--booked">
	<div class="mini-room__title"><?php echo esc_html( $room_type->get( 'title' ) ); ?></div>

	<?php echo $room_stay->get_total_price(); ?>

</div><!--/.mini-room--booked-->
