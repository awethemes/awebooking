<?php
/**
 * The Template for shortcode cart.
 *
 * This template can be overridden by copying it to yourtheme/awebooking/cart.php.
 *
 * @author 		Awethemes
 * @package 	Awethemes/Templates
 * @version     3.0.0
 */

use AweBooking\Support\Period;
use AweBooking\Booking\Request;
use AweBooking\Hotel\Service;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
$index = 1;
$cart = awebooking( 'cart' );
$cart_collection = $cart->get_contents();
?>
<?php if ( count( $cart_collection ) > 0 ) : ?>
<div class="awebooking-cart">
	<div class="awebooking-cart-items awebooking-accordion">
		<?php foreach ( $cart_collection as $row_id => $cart_item ) : $room_type = $cart_item->model(); ?>
			<h5 class="awebooking-accordion__header">
				<?php
					/* translators: %s: booking room */
					printf( esc_html__( 'Room %1$s: %2$s' ), intval( $index ), esc_html( $room_type->get_title() ) );
				?>
			</h5>
			<div class="awebooking-accordion__content">

				<div class="awebooking-cart-item">
					<div class="awebooking-cart-item__content">
						<div class="awebooking-cart-item__media">
							<a href="<?php echo esc_url( get_permalink( $room_type->get_id() ) ); ?>" title="<?php echo esc_attr( $room_type->get_title() ); ?>">
								<?php echo awebooking_get_room_type_thumbnail( 'awebooking_catalog', intval( $room_type->get_id() ) ); // WPCS: xss ok. ?>
							</a>
						</div>

						<div class="awebooking-cart-item__info">
							<h2 class="awebooking-cart-item__title">
								<a href="<?php echo esc_url( get_permalink( $room_type->get_id() ) ); ?>" rel="bookmark">
									<?php echo esc_html( $room_type->get_title() ); ?>
								</a>
							</h2>

							<p class="awebooking-cart-item__price">
								<strong><?php esc_html_e( 'Total:', 'awebooking' ); ?></strong><?php echo $cart_item->get_total(); ?>
							</p>
						</div>
					</div>
					<div class="awebooking-cart-item__reservation">
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
						<h6 class="awebooking-cart-item__reservation-title"><?php esc_html_e( 'Reservation', 'awebooking' ); ?></h6>
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
					</div>
					<?php
						$edit_link   = add_query_arg( [
							'booking-action' => 'edit',
							'rid'            => $row_id,
						], awebooking_get_page_permalink( 'booking' ) );

						$remove_link = add_query_arg( [
							'booking-action' => 'remove',
							'rid'            => $row_id,
						], get_permalink() );
					?>
					<div class="awebooking-cart-item__buttons">
						<a class="awebooking-cart-item__edit" href="<?php echo esc_url( $edit_link ); ?>">
							<?php esc_html_e( 'Edit', 'awebooking' ); ?>
						</a>
						<a class="awebooking-cart-item__remove" href="<?php echo esc_url( $remove_link ); ?>">
							<?php esc_html_e( 'Remove', 'awebooking' ); ?>
						</a>
					</div>
				</div>
			</div>
			<?php $index++; ?>
		<?php endforeach; ?>
	</div>

	<?php do_action( 'awebooking/cart_contents' ); ?>

	<table class="awebooking-cart__total">
		<tbody>
			<tr>
				<td class="text-right"><b><?php esc_html_e( 'Total', 'awebooking' ); ?></b></td>
				<td><b><?php echo $cart->total(); ?></b></td>
			</tr>
		</tbody>
	</table>

	<?php $checkout_link = get_permalink( absint( awebooking_option( 'page_checkout' ) ) ); ?>
	<div class="awebooking-cart__buttons">
		<a class="btn button awebooking-button" href="<?php echo esc_url( $checkout_link ); ?>"><?php esc_html_e( 'Proceed to Checkout', 'awebooking' ); ?></a>
	</div>
</div>
<?php else : ?>
	<p class="awebooking-empty-cart"><?php esc_html_e( 'No rooms in your booking.', 'awebooking' ); ?></p>
<?php endif; ?>
