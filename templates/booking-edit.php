<?php
/**
 * The Template for displaying booking informations.
 *
 * This template can be overridden by copying it to yourtheme/awebooking/booking.php.
 *
 * @author 		Awethemes
 * @package 	AweBooking/Templates
 * @version     3.0.0
 */

use AweBooking\Support\Formatting;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

do_action( 'awebooking/template_notices' );
$room_type = $cart_item->model();
?>
<div class="awebooking-informations">

	<h1 class="awebooking-informations__title"><?php printf( esc_html__( 'Edit booking for %s', 'awebooking' ),  esc_html( $room_type->get_title() ) ); // WPCS: xss ok. ?></h1>

	<div class="table-responsive">
		<table class="awebooking-informations__table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Arriving On', 'awebooking' ); ?></th>
					<th><?php esc_html_e( 'Departing On', 'awebooking' ); ?></th>
					<th><?php esc_html_e( 'Night', 'awebooking' ); ?></th>
					<th><?php esc_html_e( 'Group Size', 'awebooking' ); ?></th>
					<th><?php esc_html_e( 'Booking Cost', 'awebooking' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php print Formatting::date_format( $booking_request->get_check_in() ); // WPCS: xss ok. ?></td>
					<td><?php print Formatting::date_format( $booking_request->get_check_out() ); // WPCS: xss ok. ?></td>
					<td><?php print $booking_request->get_nights(); // WPCS: xss ok. ?></td>
					<td>
					<?php printf( _nx( '%s adult', '%s adults', (int) $booking_request->get_adults(), 'adult(s) information', 'awebooking' ), number_format_i18n( (int) $booking_request->get_adults() ) ); // WPCS: xss ok.?>

					<?php
						if ( $booking_request->get_children() ) {
							printf( _nx( ' & %s child', ' & %s children', (int) $booking_request->get_children(), 'child(ren) information', 'awebooking' ), number_format_i18n( (int) $booking_request->get_children() ) ); // WPCS: xss ok.
						}
					?>
					</td>
					<td><?php print $room_type->get_buyable_price( $cart_item->options ); // WPCS: xss ok. ?></td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="awebooking-informations__wrapper">
		<form action="" id="awebooking-booking-form" class="awebooking-service__wrapper" method="POST">
			<div class="awebooking-informations__content">
				<?php if ( $room_type['service_ids'] ) : ?>
				<table class="awebooking-informations__table">
					<thead>
						<tr>
							<th colspan="2" class="text-left"><?php esc_html_e( 'Extra Services', 'awebooking' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td colspan="2">
								<div class="awebooking-service" id="awebooking-service">
									<?php foreach ( $room_type->get_services() as $service ) : ?>
										<?php $mandatory = ( 'mandatory' === $service->get_type()  ) ? 'checked="checked" disabled="disabled"' : ''; ?>
										<div class="awebooking-service__item">
											<input type="checkbox" id="extra_id_<?php echo esc_attr( $service->get_id() ); ?>" <?php echo esc_attr( $mandatory ); ?> name="awebooking_services[]" value="<?php echo esc_attr( $service->get_id() ); ?>" <?php if ( in_array( $service->get_id(), $cart_item->options['extra_services'] ) ) echo 'checked="checked"'; ?>>

											<label for="extra_id_<?php echo esc_attr( $service->get_id() ); ?>"><?php echo esc_html( $service->get_name() ); ?></label>
											<span><?php echo $service->get_describe(); ?></span>

											<div class="awebooking-service__content">
												<?php if ( $service->get_description() ) : ?>
													<p><?php echo esc_html( $service->get_description() ); ?></p>
												<?php endif; ?>
											</div>
										</div>
									<?php endforeach; ?>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
				<?php endif; ?>

				<?php do_action( 'awebooking/booking/before_total_cost', $booking_request, $room_type ); ?>

				<table class="awebooking-informations__table">
					<thead>
						<tr>
							<th colspan="2"><?php echo esc_html( awebooking( 'currency' )->get_code() ); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><?php esc_html_e( 'TOTAL COST', 'awebooking' ); ?></td>
							<td id="awebooking-total-cost"><?php print $room_type->get_buyable_price( $cart_item->options ); // WPCS: xss ok. ?></td>
						</tr>
					</tbody>
				</table>

				<div class="text-right">
					<button type="submit" class="btn button awebooking-button">
						<?php esc_html_e( 'Save','awebooking' ); ?>
					</button>

					<a href="<?php echo esc_url( awebooking_get_page_permalink( 'checkout' ) ); ?>" class="btn button awebooking-button">
						<?php esc_html_e( 'Checkout', 'awebooking' ); ?>
					</a>
				</div>
			</div>

			<input type="hidden" name="booking-action" value="edit">
			<input type="hidden" name="room-type" value="<?php echo esc_attr( $room_type->get_id() ); ?>">
			<input type="hidden" name="start-date" value="<?php echo esc_attr( $booking_request->get_check_in()->format( 'Y-m-d' ) ); ?>">
			<input type="hidden" name="end-date" value="<?php echo esc_attr( $booking_request->get_check_out()->format( 'Y-m-d' ) ); ?>">
			<input type="hidden" name="children" value="<?php echo esc_attr( $booking_request->get_children() ); ?>">
			<input type="hidden" name="adults" value="<?php echo esc_attr( $booking_request->get_adults() ); ?>">

			<?php wp_nonce_field( 'awebooking-edit-booking-nonce' ); ?>
		</form>
	</div>
</div>
