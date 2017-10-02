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

if ( isset( $message_error ) || $availability->unavailable() ) : ?>

<p><?php //echo isset( $message_error ) ? $message_error : ''; ?></p>

<?php else: ?>

<div class="awebooking-informations">

	<h1 class="awebooking-informations__title"><?php printf( esc_html__( 'Booking for %s', 'awebooking' ),  $room_type->get_title() ); // WPCS: xss ok. ?></h1>

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
					<td><?php print Formatting::date_format( $availability->get_check_in() ); // WPCS: xss ok. ?></td>
					<td><?php print Formatting::date_format( $availability->get_check_out() ); // WPCS: xss ok. ?></td>
					<td><?php print $availability->get_nights(); // WPCS: xss ok. ?></td>
					<td>
					<?php printf( _nx( '%s adult', '%s adults', (int) $availability->get_adults(), 'adult(s) information', 'awebooking' ), number_format_i18n( (int) $availability->get_adults() ) ); // WPCS: xss ok.?>

					<?php
						if ( $availability->get_children() ) {
							printf( _nx( ' & %s child', ' & %s children', (int) $availability->get_children(), 'child(ren) information', 'awebooking' ), number_format_i18n( (int) $availability->get_children() ) ); // WPCS: xss ok.
						}
					?>
					</td>
					<td><?php print $availability->get_price(); // WPCS: xss ok. ?></td>
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
											<input type="checkbox" id="extra_id_<?php echo esc_attr( $service->get_id() ); ?>" <?php echo esc_attr( $mandatory ); ?> name="awebooking_services[]" value="<?php echo esc_attr( $service->get_id() ); ?>">

											<label for="extra_id_<?php echo esc_attr( $service->get_id() ); ?>"><?php echo esc_html( $service->get_name() ); ?></label>
											<span><?php echo $service->get_describe(); ?></span>

											<div class="awebooking-service__content">
												<?php if ( $service->get_description() ) : ?>
													<p><?php echo esc_html( $service->get_description() ) ?></p>
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

				<?php do_action( 'awebooking/booking/before_total_cost', $availability, $room_type ); ?>

				<table class="awebooking-informations__table">
					<thead>
						<tr>
							<th colspan="2"><?php echo esc_html( awebooking( 'currency' )->get_code() ); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><?php esc_html_e( 'TOTAL COST', 'awebooking' ); ?></td>
							<td id="awebooking-total-cost"><?php print $availability->get_total_price(); ?></td>
						</tr>
					</tbody>
				</table>

				<div class="text-right">
					<button type="submit" class="btn button awebooking-button">
						<?php esc_html_e( 'Book another room','awebooking' ); ?>
					</button>
					<input type="submit" name="go-to-checkout" class="btn button awebooking-button" value="<?php esc_attr_e( 'Checkout','awebooking' ); ?>">
				</div>
			</div>
			<input type="hidden" name="booking-action" value="add">
			<input type="hidden" name="room-type" value="<?php echo esc_attr( $room_type->get_id() ); ?>">
			<input type="hidden" name="start-date" value="<?php echo esc_attr( $availability->get_check_in()->format( 'Y-m-d' ) ); ?>">
			<input type="hidden" name="end-date" value="<?php echo esc_attr( $availability->get_check_out()->format( 'Y-m-d' ) ); ?>">
			<input type="hidden" name="children" value="<?php echo esc_attr( $availability->get_request()->get_children() ); ?>">
			<input type="hidden" name="adults" value="<?php echo esc_attr( $availability->get_request()->get_adults() ); ?>">	
			<?php wp_nonce_field( 'awebooking-add-booking-nonce' ); ?>
		</form>
	</div>
</div>

<?php endif ?>
