<?php
/**
 * The template for displaying room price in the template-parts/archive/content.php template
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/template-parts/archive/price.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room_type;

if ( ! $room_type->get( 'rack_rate' ) ) {
	return;
}

?>

<p class="list-room__price">
	<?php
	/* translators: %s room price */
	printf( esc_html__( 'Start from %s/night', 'awebooking' ), '<span>' . abrs_format_price( $room_type->get( 'rack_rate' ) ) . '</span>' ); // WPCS: xss ok.
	?>
</p>
