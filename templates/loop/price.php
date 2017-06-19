<?php
/**
 * Loop Price
 *
 * This template can be overridden by copying it to yourtheme/awebooking/loop/price.php.
 *
 * @author 		Awethemes
 * @package 	AweBooking/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $room_type;
?>

<?php if ( $price_html = $room_type->get_base_price() ) : ?>
	<p class="awebooking-loop-room-type__price"><?php printf( esc_html__( 'Start from %s / Night', 'awebooking' ), '<span>' . $price_html . '</span>'); // WPCS: xss ok. ?></p>
<?php endif; ?>
