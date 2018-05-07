<?php
/**
 * Single Room type Price, including microdata for SEO
 *
 * This template can be overridden by copying it to yourtheme/awebooking/single-room-type/price.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
global $room_type;

if ( $room_type->get( 'rack_rate' ) ) : ?>
	<p class="awebooking-room-type__price"><?php printf( esc_html__( 'Start from %s/night', 'awebooking' ), '<span>' . abrs_format_price( $room_type->get( 'rack_rate' ) ) . '</span>' ); ?></p>
<?php endif; ?>
