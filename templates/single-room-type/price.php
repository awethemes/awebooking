<?php
/**
 * Single Room type Price, including microdata for SEO
 *
 * This template can be overridden by copying it to yourtheme/awebooking/single-room-type/price.php.
 *
 * @author  Awethemes
 * @package AweBooking/Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $room_type;

if ( ! $room_type->get_base_price()->is_zero() ) : ?>
	<p class="awebooking-room-type__price"><?php printf( esc_html__( 'Start from %s/night', 'awebooking' ), '<span>' . $room_type->get_base_price() . '</span>' ); ?></p>
<?php endif; ?>
