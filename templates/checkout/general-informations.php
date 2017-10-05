<?php
/**
 * The Template for displaying check form.
 *
 * This template can be overridden by copying it to yourtheme/awebooking/checkout/general-informations.php.
 *
 * @author 		awethemes
 * @package 	AweBooking/Templates
 * @version     3.0.0
 */

use AweBooking\Support\Period;
use AweBooking\Booking\Request;
use AweBooking\Hotel\Service;
use AweBooking\Support\Formatting;
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$index = 1;
$cart = awebooking( 'cart' );
$cart_collection = $cart->get_contents();
?>
<div class="awebooking-booking-items">
	<?php foreach ( $cart_collection as $row_id => $cart_item ) :  $room_type = $cart_item->model(); ?>
		<?php
			$period  = new Period( $cart_item->options['check_in'], $cart_item->options['check_out'], true );
			$request = new Request( $period, [
				'room-type' => $room_type->get_id(),
				'adults'    => $cart_item->options['adults'],
				'children'  => $cart_item->options['children'],
				'extra_services' => $cart_item->options['extra_services'],
			] );
			$services = collect( awebooking_map_instance(
				array_keys( $request->get_services() ),
				Service::class
			) );
		?>
		<table class="awebooking-booking-item">
			<tbody>
				<tr>
					<th class="awebooking-booking-item__number">
						<b>
						<?php
							/* translators: %s: booking room */
							printf( esc_html__( 'Room %1$s' ), intval( $index ) );
						?>
						</b>
					</th>
					<th class="awebooking-booking-item__room"><b><?php echo esc_html( $room_type->get_title() ); ?></b></th>
					<td class="awebooking-booking-item__reservation">
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
							<?php echo $request->get_fomatted_guest_number(); ?>
						</p>
						<?php if ( $services->implode( 'name', ', ' ) ) : ?>
							<p>
								<strong><?php esc_html_e( 'Extra service(s):', 'awebooking' ); ?></strong>
								<?php echo esc_html( $services->implode( 'name', ', ' ) ); ?>
							</p>
						<?php endif; ?>
					</td>
					<td><?php echo $room_type->get_buyable_price( $cart_item->options ); ?></td>
				</tr>
			</tbody>
		</table>
		<?php $index++; ?>
	<?php endforeach; ?>
</div>

<table>
	<tbody>
		<tr>
			<td colspan="3" class="text-right"><b><?php esc_html_e( 'Total', 'awebooking' ); ?></b></td>
			<td><b><?php echo $cart->total(); ?></b></td>
		</tr>
	</tbody>
</table>
