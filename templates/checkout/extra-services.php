<?php
/**
 * The Template for displaying check form.
 *
 * This template can be overridden by copying it to yourtheme/awebooking/check-availability-form.php.
 *
 * @author 		awethemes
 * @package 	AweBooking/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<?php if ( $extra_services_name ) : ?>
<table class="table">
	<thead>
		<tr>
			<th colspan="3"><?php esc_html_e( 'Extra services', 'awebooking' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td colspan="2"><?php echo esc_html( implode( $extra_services_name , ', ') ); ?></td>
			<td><?php print $availability->get_extra_services_price(); // WPCS: xss ok.?></td>
		</tr>
		<tr>
			<td class="text-right" colspan="2"><b><?php esc_html_e( 'Services Subtotal', 'awebooking' ); ?></b></td>
			<td><b><?php print $availability->get_extra_services_price(); // WPCS: xss ok.?></b></td>
		</tr>
	</tbody>
</table>
<?php endif; ?>
