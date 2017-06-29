<?php
/**
 * The Template for displaying check form.
 *
 * This template can be overridden by copying it to yourtheme/awebooking/checkout/general-informations.php.
 *
 * @author 		awethemes
 * @package 	AweBooking/Templates
 * @version     1.0.0
 */

use AweBooking\Support\Formatting;
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<table class="">
	<tbody>
		<tr>
			<th><b><?php esc_html_e( 'Reservation', 'awebooking' ); ?></b></th>
			<td><?php printf( __( '<b>%1$s</b> from %2$s to %3$s, %4$s nights', 'awebooking' ), $room_type->get_title(), Formatting::date_format( $availability->get_check_in() ), Formatting::date_format( $availability->get_check_out() ), $availability->get_nights() ); // WPCS: xss ok. ?></td>
			<td><?php print $availability->get_price(); // WPCS: xss ok.?></td>
		</tr>
		<tr>
			<th><b><?php esc_html_e( 'Extra services', 'awebooking' ); ?></b></th>
			<td><?php echo esc_html( implode( $extra_services_name , ', ') ); ?></td>
			<td><?php print $availability->get_extra_services_price(); // WPCS: xss ok.?></td>
		</tr>
		<tr>
			<td colspan="2" class="text-right"><b><?php esc_html_e( 'Total', 'awebooking' ); ?></b></td>
			<td><b><?php print $availability->get_total_price(); // WPCS: xss ok.?></b></td>
		</tr>
	</tbody>
</table>
