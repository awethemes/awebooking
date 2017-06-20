<?php
/**
 * The Template for checkout page.
 *
 * This template can be overridden by copying it to yourtheme/awebooking/checkout.php.
 *
 * @author 		Awethemes
 * @package 	Awethemes/Templates
 * @version     1.0.0
 */

use AweBooking\Support\Formatting;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>


<?php do_action( 'awebooking/template_notices' ); ?>

<?php if ( isset( $availability ) && $availability->available() ) : ?>
<table class="table">
	<thead>
		<tr>
			<th colspan="3"><?php printf( esc_html__( 'Room type: %s', 'awebooking' ), $room_type->get_title() ); ?></th>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Room number', 'awebooking' ); ?></th>
			<th><?php esc_html_e( 'Detail', 'awebooking' ); ?></th>
			<th><?php esc_html_e( 'Price', 'awebooking' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>room-1</td>
			<td>June, 19 2017</td>
			<td><?php print $availability->get_price(); // WPCS: xss ok.?></td>
		</tr>
		<tr>
			<td class="text-right" colspan="2"><b><?php esc_html_e( 'Subtotal', 'awebooking' ); ?></b></td>
			<td><b><?php print $availability->get_price(); // WPCS: xss ok.?></b></td>
		</tr>
	</tbody>
</table>

<?php do_action( 'awebooking/checkout/extra_service_details', $availability ); ?>

<table class="table" style="margin-bottom: 50px;">
	<tbody>
		<tr>
			<td class="text-right" colspan="2"><b><?php esc_html_e( 'Total', 'awebooking' ); ?></b></td>
			<td><b><?php print $availability->get_total_price(); // WPCS: xss ok.?></b></td>
		</tr>
	</tbody>
</table>

<?php do_action( 'awebooking/checkout/customer_form' ); ?>

<?php endif ?>
