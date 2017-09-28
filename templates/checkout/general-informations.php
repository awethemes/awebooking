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

use AweBooking\Support\Period;
use AweBooking\Booking\Request;
use AweBooking\Hotel\Service;
use AweBooking\Support\Formatting;
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$std = 1;
$cart = awebooking( 'cart' );
$cart_collection = $cart->get_contents();
?>
<?php foreach ( $cart_collection as $row_id => $cart_item ) : ?>
	<?php
		$period  = new Period( $cart_item->options['check_in'], $cart_item->options['check_out'], true );
		$request = new Request( $period, [
			'room-type' => $cart_item->model()->get_id(),
			'adults'    => $cart_item->options['adults'],
			'children'  => $cart_item->options['children'],
			'extra_services' => $cart_item->options['extra_services'],
		] );
		$services = collect( awebooking_map_instance(
			array_keys( $request->get_services() ),
			Service::class
		) );
	?>
	<table class="awebooking-booking-items">
		<tbody>
			<tr>
				<th><b><?php echo esc_html( intval( $std ) ); ?></b></th>
				<th><b><?php echo esc_html( $cart_item->model()->get_title() ); ?></b></th>
				<td>
					<p>
						<strong><?php esc_html_e( 'Check-in:', 'awebooking' ); ?></strong>
						<?php echo esc_html( $request->get_check_in()->toDateString() ); ?>
					</p>
					<p>
						<strong><?php esc_html_e( 'Check-out:', 'awebooking' ); ?></strong>
						<?php echo esc_html( $request->get_check_out()->toDateString() ); ?>
					</p>
					<p>
						<strong><?php esc_html_e( 'Night(s):', 'awebooking' ); ?></strong>
						<?php echo esc_html( $request->get_nights() ); ?>
					</p>
					<p>
						<strong><?php esc_html_e( 'Guest(s):', 'awebooking' ); ?></strong>
						<?php echo esc_html( $request->get_fomatted_guest_number() ); ?>
					</p>
					<?php if ( $services->implode( 'name', ', ' ) ) : ?>
						<p>
							<strong><?php esc_html_e( 'Extra service(s):', 'awebooking' ); ?></strong>
							<?php echo esc_html( $services->implode( 'name', ', ' ) ); ?>
						</p>
					<?php endif; ?>
				</td>
				<td><?php echo esc_html( $cart_item->model()->get_buyable_price( $cart_item->options ) ); ?></td>
			</tr>
		</tbody>
	</table>
	<?php $std++; ?>
<?php endforeach; ?>

<table>
	<tbody>
		<tr>
			<td colspan="3" class="text-right"><b><?php esc_html_e( 'Total', 'awebooking' ); ?></b></td>
			<td><b><?php echo esc_html( $cart->total() ); ?></b></td>
		</tr>
	</tbody>
</table>
