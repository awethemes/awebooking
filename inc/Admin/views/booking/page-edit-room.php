<?php
/**
 *
 * @var \AweBooking\Model\Booking           $booking
 * @var \AweBooking\Model\Booking\Room_Item $room_item
 */

$current_action = abrs_http_request()->get( 'action' );

$old_input = awebooking( 'session' )->get_old_input();
if ( ! empty( $old_input ) ) {
	$controls->fill( $old_input );
}

$room_type = abrs_get_room_type( $room_item->get( 'room_type_id' ) );
$rate_plan = abrs_get_rate( $room_item->get( 'rate_plan_id' ) );

?><div class="wrap">
	<h1 class="wp-heading-inline screen-reader-text"><?php esc_html_e( 'Update Room Stay', 'awebooking' ); ?></h1>
	<hr class="wp-header-end">

	<div class="abrs-card abrs-card--page">
		<form method="POST" action="<?php echo esc_url( abrs_admin_route( "booking-room/{$room_item->get_id()}" ) ); ?>">
			<?php wp_nonce_field( 'update_room_stay' ); ?>
			<input type="hidden" name="_method" value="PUT">
			<input type="hidden" name="_refer" value="<?php echo esc_attr( $booking->get_id() ); ?>">
			<input type="hidden" name="_action" value="<?php echo esc_attr( $current_action ); ?>">

			<div class="abrs-card__header">
				<h2 class=""><?php esc_html_e( 'Update Room Stay', 'awebooking' ); ?></h2>
				<span><?php esc_html_e( 'Reference', 'awebooking' ); ?> <a href="<?php echo esc_url( get_edit_post_link( $booking->get_id() ) ); ?>">#<?php echo esc_html( $booking->get_booking_number() ); ?></a></span>
			</div>

			<div class="cmb2-wrap awebooking-wrap abrs-card__body">
				<div class="cmb2-metabox">
					<div class="cmb-row">
						<div class="cmb-th"><label><?php echo esc_html__( 'Room Type', 'awebooking' ); ?></label></div>

						<div class="cmb-td">
							<strong><?php echo esc_html( abrs_optional( $room_type )->get( 'title' ) ); ?></strong>
							<span>(<?php echo esc_html( abrs_optional( $room_item )->get( 'name' ) ); ?>)</span>
						</div>
					</div>

					<div class="cmb-row">
						<div class="cmb-th"><label><?php echo esc_html__( 'Rate Plan', 'awebooking' ); ?></label></div>

						<div class="cmb-td">
							<?php if ( $rate_plan ) : ?>
								<strong><?php echo esc_html( $rate_plan->get_name() ); ?></strong>
							<?php endif; ?>
						</div>
					</div>

					<div class="cmb-row">
						<div class="cmb-th"><label><?php echo esc_html__( 'Stay', 'awebooking' ); ?></label></div>

						<div class="cmb-td">
							<span>
								<i class="aficon aficon-moon" style="vertical-align: middle;"></i>
								<?php /* translators: %s number of night */ ?>
								<?php echo sprintf( _n( 'one night', '%s nights', $room_item->get_nights_stayed(), 'awebooking' ), esc_html( number_format_i18n( $room_item->get_nights_stayed() ) ) ); // @codingStandardsIgnoreLine. ?>
							</span>

							<span class="abrs-badge" style="vertical-align: baseline;"><?php echo esc_html( $room_item->get( 'check_in' ) ); ?></span>
							<span><?php echo esc_html_x( 'to', 'separator between dates', 'awebooking' ); ?></span>
							<span class="abrs-badge" style="vertical-align: baseline;"><?php echo esc_html( $room_item->get( 'check_out' ) ); ?></span>

							<?php if ( 'change-timespan' !== $current_action ) : ?>
								<a href="<?php echo esc_url( rawurldecode( add_query_arg( 'action', 'change-timespan' ) ) ); ?>"><?php echo esc_html__( 'Change', 'awebooking' ); ?></a>
							<?php endif; ?>
						</div>
					</div>

					<?php if ( 'change-timespan' === $current_action ) : ?>

						<?php $controls->show_field( 'change_timespan' ); ?>

					<?php else : ?>

						<?php
						foreach ( [ 'adults', 'children', 'infants', 'subtotal', 'total' ] as $field ) {
							if ( isset( $controls[ $field ] ) ) {
								$controls->show_field( $field );
							}
						}
						?>

					<?php endif ?>
				</div>
			</div>

			<div class="abrs-card__footer submit abrs-text-right">
				<a href="<?php echo esc_url( get_edit_post_link( $booking->get_id() ) ); ?>" class="button button-link"><?php echo esc_html__( 'Cancel', 'awebooking' ); ?></a>
				<button type="submit" class="button abrs-button"><?php echo esc_html__( 'Save', 'awebooking' ); ?></button>
			</div>
		</form>
	</div>
</div><!-- /.wrap -->
