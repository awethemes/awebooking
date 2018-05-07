<?php
/**
 * Loop Price
 *
 * This template can be overridden by copying it to yourtheme/awebooking/loop/view-more.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<a class="awebooking-loop-room-type__button" href="<?php echo esc_url( get_the_permalink() ); ?>"><?php esc_html_e( 'View more infomation', 'awebooking' ); ?></a><br />
