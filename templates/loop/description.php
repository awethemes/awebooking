<?php
/**
 * Loop Price
 *
 * This template can be overridden by copying it to yourtheme/awebooking/loop/description.php.
 *
 * @author 		Awethemes
 * @package 	AweBooking/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $room_type;

if ( ! $room_type->get_description() ) {
	return;
}
?>
<div class="awebooking-loop-room-type__desc">
	<?php print wp_trim_words( $room_type->get_description(), 25, '...' ); // WPCS: xss ok. ?>
</div>
