<?php
/**
 *
 * @var \AweBooking\Model\Booking           $booking
 * @var \AweBooking\Model\Booking\Room_Item $room_item
 */

use AweBooking\Constants;

$current_action = abrs_http_request()->get( 'action' );

$old_input = awebooking( 'session' )->get_old_input();
if ( ! empty( $old_input ) ) {
	$controls->fill( $old_input );
}

$room_type = abrs_get_room_type( $room_item->get( 'room_type_id' ) );
$rate_plan = abrs_get_rate( $room_item->get( 'rate_plan_id' ) );

// Check rooms.
$timespan = $room_item->get_timespan();
if ( 'swap' === $current_action && $timespan ) {
	$rooms = $room_type->get_rooms();

	// Reject current rooms.
	foreach ( $rooms as $index => $room ) {
		if ( $room->get_id() === (int) $room_item->get( 'room_id' ) ) {
			$rooms->forget( $index );
			break;
		}
	}

	if ( count( $rooms ) > 1 ) {
		$room_response = abrs_check_room_states( $rooms, $room_item->get_timespan(), Constants::STATE_AVAILABLE );

		if ( ! is_wp_error( $room_response ) ) {
			$avai_rooms = $room_response
				->get_included()
				->map( function ( $res ) {
					return $res['resource']->get_reference();
				} );

			$controls
				->get_field( 'swap_to_room' )
				->set_prop( 'options', $avai_rooms->pluck( 'name', 'id' )->all() );
		}
	}
}

?><div class="wrap">
	<h1 class="wp-heading-inline screen-reader-text">
		<?php if ( 'swap' === $current_action ) : ?>
			<?php esc_html_e( 'Swap Room', 'awebooking' ); ?>
		<?php else : ?>
			<?php esc_html_e( 'Update Room Stay', 'awebooking' ); ?>
		<?php endif; ?>
	</h1>
	<hr class="wp-header-end">

	<div class="abrs-card abrs-card--page">
		<form id="awebooking-edit-room-form" method="POST" action="<?php echo esc_url( abrs_admin_route( "booking-room/{$room_item->get_id()}" ) ); ?>">
			<?php wp_nonce_field( 'update_room_stay' ); ?>
			<input type="hidden" name="_method" value="PUT">
			<input type="hidden" name="_refer" value="<?php echo esc_attr( $booking->get_id() ); ?>">
			<input type="hidden" name="_action" value="<?php echo esc_attr( $current_action ); ?>">
			<input type="hidden" name="_room_type" value="<?php echo esc_attr( $room_type->get_id() ); ?>">

			<div class="abrs-card__header">
				<h2 class="">
					<?php if ( 'swap' === $current_action ) : ?>
						<?php esc_html_e( 'Swap Room', 'awebooking' ); ?>
					<?php else : ?>
						<?php esc_html_e( 'Update Room Stay', 'awebooking' ); ?>
					<?php endif; ?>
				</h2>

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

					<?php elseif ( 'swap' === $current_action ) : ?>

						<?php $controls->show_field( 'swap_to_room' ); ?>

					<?php else : ?>

						<?php
						foreach ( [ 'adults', 'children', 'infants' ] as $field ) {
							if ( isset( $controls[ $field ] ) ) {
								$controls->show_field( $field );
							}
						}
						?>

					<?php endif ?>

					<?php if ( 'swap' !== $current_action ) : ?>
						<div class="cmb-row" id="js-apply-new-prices" style="display: none;">
							<div class="cmb-th"></div>

							<div class="cmb-td">
								<span><?php echo esc_html__( 'Detect new price:', 'awebooking' ); ?></span>
								<a id="js-apply-new-price" href="#" title="<?php echo esc_html__( 'Click to apply new price', 'awebooking' ); ?>"><strong class="subtotal"></strong></a>
							</div>
						</div>

						<?php $controls->show_field( 'subtotal' ); ?>
						<?php $controls->show_field( 'total' ); ?>

					<?php endif; ?>

				</div>
			</div>

			<div class="abrs-card__footer submit abrs-text-right">
				<a href="<?php echo esc_url( get_edit_post_link( $booking->get_id() ) ); ?>" class="button button-link"><?php echo esc_html__( 'Cancel', 'awebooking' ); ?></a>
				<button type="submit" class="button abrs-button"><?php echo esc_html__( 'Save', 'awebooking' ); ?></button>
			</div>
		</form>
	</div>
</div><!-- /.wrap -->

<script>
	(function ($) {
		'use strict';

		$(function () {
			var xhr, _flatpickr

			var form = $('#awebooking-edit-room-form');
			var checkInInput = $('[name="change_check_in"]', form)[0]

			var refreshCosts = function () {
				var inputName = $(this).attr('name');
				if ('total' === inputName || 'subtotal' === inputName) {
					return;
				}

				if (xhr && xhr.readyState !== 4) {
					xhr.abort();
				}

				xhr = awebooking.ajax('GET', '/ajax/rates/check', {
					booked: <?php echo esc_attr( $room_item->get_id() ); ?>,
					room_type: $('[name="_room_type"]', form).val(),
					adults: $('[name="adults"]', form).val(),
					children: $('[name="children"]', form).val(),
					infants: $('[name="infants"]', form).val(),
					check_in: $('[name="change_check_in"]', form).val(),
					check_out: $('[name="change_check_out"]', form).val(),
				}, function (response) {
					var data = response.data;
					var subtotal = data.prices.rate;
					var currentSubtotal = $('[name="subtotal"]', form).val();

					var element = $('#js-apply-new-prices');
					if (subtotal != currentSubtotal) {
						element.show();
						element.find('.subtotal').html(awebooking.formatPrice(subtotal));
						$('#js-apply-new-price').attr('data-amount', subtotal)
					}
				});
			};

			$('[name="adults"], [name="children"], [name="infants"]', form).on('change', refreshCosts);

			if (checkInInput && checkInInput._flatpickr) {
				_flatpickr = checkInInput._flatpickr;

				_flatpickr.config.onChange.push(function (selectedDates) {
					if (selectedDates.length === 2) {
						refreshCosts();
					}
				});
			}

			$('#js-apply-new-price').on('click', function (e) {
				e.preventDefault();
				var amount = $(this).attr('data-amount');
				if (amount) {
					$('[name="subtotal"]', form).val(amount)
				}
			});
		});
	})(jQuery);
</script>
