<?php
/**
 * Loop Price
 *
 * This template can be overridden by copying it to yourtheme/awebooking/loop/view-more.php.
 *
 * @author 		Awethemes
 * @package 	AweBooking/Templates
 * @version     1.0.0
 */

use AweBooking\Support\Date_Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$link = get_the_permalink();

if ( is_check_availability_page() ) {
	$default_args = Date_Utils::get_booking_request_query( array( 'room-type' => get_the_ID() ) );
	$link = add_query_arg( $default_args, get_the_permalink() );
}
?>
<a class="awebooking-loop-room-type__button" href="<?php echo esc_url( $link ); ?>"><?php esc_html_e( 'View more infomation', 'awebooking' ); ?></a><br />
