<?php
use AweBooking\Model\Service;

$room_items    = $the_booking->get_rooms();
$service_items = $the_booking->get_services();
?><style type="text/css">
	#awebooking-booking-rooms .hndle,
	#awebooking-booking-rooms .handlediv { display: none;}
	#awebooking-booking-data.closed .inside { display: block !important; }
</style>

<div class="booking-sections">
	<div>
		<table class="awebooking-table abrs-booking-rooms widefat fixed">
			<thead>
				<tr>
					<th style="width: 20%;"><?php echo esc_html__( 'Room Type', 'awebooking' ); ?></th>
					<th style="width: 15%;"><?php echo esc_html__( 'Single_Rate Plan', 'awebooking' ); ?></th>
					<th style="width: 5%;"><span class="aficon aficon-moon tippy" title="<?php echo esc_html__( 'Nights', 'awebooking' ); ?>"></span><span class="screen-reader-text"><?php echo esc_html__( 'Nights', 'awebooking' ); ?></span></th>
					<th style="width: 10%;"><?php echo esc_html__( 'Check In', 'awebooking' ); ?></th>
					<th style="width: 10%;"><?php echo esc_html__( 'Check Out', 'awebooking' ); ?></th>
					<th style="width: 7%;"><?php echo esc_html__( 'Adults', 'awebooking' ); ?></th>

					<?php if ( abrs_children_bookable() ) : ?>
						<th style="width: 7%;"><?php echo esc_html__( 'Children', 'awebooking' ); ?></th>
					<?php endif ?>

					<?php if ( abrs_infants_bookable() ) : ?>
						<th style="width: 7%;"><?php echo esc_html__( 'Infants', 'awebooking' ); ?></th>
					<?php endif ?>

					<th class="abrs-text-right"><span><?php esc_html_e( 'Price', 'awebooking' ); ?></span></th>
				</tr>
			</thead>

			<tbody>
				<?php if ( abrs_blank( $room_items ) ) : ?>

					<tr>
						<td colspan="9">
							<p class="awebooking-no-items"><?php esc_html_e( 'No rooms found', 'awebooking' ); ?></p>
						</td>
					</tr>

				<?php else : ?>
					<?php foreach ( $room_items as $item ) : ?>
						<?php
						$room_type = '';

						$timespan = $item->get_timespan();
						$action_link = abrs_admin_route( "/booking-room/{$item->get_id()}" );
						?>

						<tr>
							<td>
								<?php
								$thumbnail = '<span class="abrs-no-image"></span>';
								if ( $room_type && has_post_thumbnail( $room_type->get_id() ) ) {
									$thumbnail = get_the_post_thumbnail( $room_type->get_id(), 'thumbnail' );
								}

								printf( '<div class="abrs-thumb-image abrs-fleft" style="margin-right: 10px;">%2$s</div>', esc_url( '#' ), $thumbnail ); // @wpcs: XSS OK.
								?>

								<strong class="row-title"><?php echo esc_html( $item->get_name() ); ?></strong>
								<span class="dp-block"><?php esc_html_e( 'Room Type', 'awebooking' ); ?></span>
							</td>

							<td>
								<strong><?php echo esc_html( $item['rate_plan_name'] ? $item->get( 'rate_plan_name' ) : esc_html__( '-', 'awebooking' ) ); ?></strong>
							</td>

							<td>
								<span class="abrs-badge">
									<?php if ( ! is_null( $timespan ) ) : ?>
										<?php echo esc_html( $timespan->get_nights() ); ?>
									<?php endif ?>
								</span>
							</td>

							<td>
								<?php if ( ! is_null( $timespan ) ) : ?>
									<?php echo esc_html( abrs_date( $timespan->get_start_date() )->date_i18n( abrs_get_date_format() ) ); ?>
								<?php endif ?>
							</td>

							<td>
								<?php if ( ! is_null( $timespan ) ) : ?>
									<?php echo esc_html( abrs_date( $timespan->get_end_date() )->date_i18n( abrs_get_date_format() ) ); ?>
								<?php endif ?>
							</td>

							<td>
								<?php echo esc_html( number_format_i18n( $item->get( 'adults' ) ) ); ?>
							</td>

							<?php if ( abrs_children_bookable() ) : ?>
								<td>
									<?php echo esc_html( $item['children'] ? number_format_i18n( $item->get( 'children' ) ) : '-' ); ?>
								</td>
							<?php endif ?>

							<?php if ( abrs_infants_bookable() ) : ?>
								<td>
									<?php echo esc_html( $item['infants'] ? number_format_i18n( $item->get( 'infants' ) ) : '-' ); ?>
								</td>
							<?php endif ?>

							<td class="abrs-text-right">
								<?php if ( $the_booking->is_editable() ) : ?>
									<div class="row-actions abrs-fleft">
										<span class="edit"><a href="<?php echo esc_url( $action_link ); ?>"><?php esc_html_e( 'Edit', 'awebooking' ); ?></a> | </span>
										<span class="trash"><a href="<?php echo esc_url( wp_nonce_url( $action_link, "delete_room_{$item->get_id()}" ) ); ?>" data-method="abrs-delete"><?php esc_html_e( 'Delete', 'awebooking' ); ?></a></span>
									</div>
								<?php endif; ?>

								<?php abrs_price( $item->get( 'total' ), $the_booking->get( 'currency' ) ); ?>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table><!-- /.awebooking-table -->
	</div>

	<div>
		<table class="awebooking-table abrs-booking-rooms widefat fixed">
			<thead>
				<tr>
					<th style="width: 20%;"><?php echo esc_html__( 'Service', 'awebooking' ); ?></th>
					<th style="width: 22%;"><?php echo esc_html__( 'Quantity', 'awebooking' ); ?></th>
					<th style="width: 22%;"><?php echo esc_html__( 'Unit price', 'awebooking' ); ?></th>
					<th class="abrs-text-right"><span><?php esc_html_e( 'Price', 'awebooking' ); ?></span></th>
				</tr>
			</thead>

			<tbody>
				<?php if ( abrs_blank( $service_items ) ) : ?>

					<tr>
						<td colspan="9">
							<p class="awebooking-no-items"><?php esc_html_e( 'No services found', 'awebooking' ); ?></p>
						</td>
					</tr>

				<?php else : ?>
					<?php foreach ( $service_items as $service_item ) : ?>
						<?php
						$action_link = abrs_admin_route( "/booking-service/{$service_item->get_id()}" );
						$service = new Service( $service_item->get( 'service_id' ) );
						?>

						<tr>
							<td>
								<?php
								$thumbnail = '<span class="abrs-no-image"></span>';
								if ( $service && has_post_thumbnail( $service->get_id() ) ) {
									$thumbnail = get_the_post_thumbnail( $service->get_id(), 'thumbnail' );
								}
								printf( '<div class="abrs-thumb-image abrs-fleft" style="margin-right: 10px;">%2$s</div>', esc_url( '#' ), $thumbnail ); // @wpcs: XSS OK.
								?>

								<strong class="row-title"><?php echo esc_html( $service_item->get_name() ); ?></strong>
								<span class="dp-block"><?php esc_html_e( 'Service', 'awebooking' ); ?></span>
							</td>

							<td><?php echo absint( $service_item->get( 'quantity' ) ); ?></td>
							<td><?php echo abrs_format_price( $service_item->get( 'price' ) ); ?></td>

							<td class="abrs-text-right">
								<?php if ( $the_booking->is_editable() ) : ?>
									<div class="row-actions abrs-fleft">
										<span class="trash"><a href="<?php echo esc_url( wp_nonce_url( $action_link, "delete_service_{$service_item->get_id()}" ) ); ?>" data-method="abrs-delete"><?php esc_html_e( 'Delete', 'awebooking' ); ?></a></span>
									</div>
								<?php endif; ?>

								<?php abrs_price( $service_item->get( 'total' ), $the_booking->get( 'currency' ) ); ?>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table><!-- /.awebooking-table -->
	</div>

	<div class="booking-section booking-section--totals abrs-clearfix">
		<table class="awebooking-table abrs-booking-totals">
			<tbody>
				<tr>
					<th><?php echo esc_html__( 'Total:', 'awebooking' ); ?></th>
					<td><?php abrs_price( $the_booking->get( 'total' ), $the_booking->get( 'currency' ) ); ?></td>
				</tr>

				<tr>
					<th><?php echo esc_html__( 'Paid:', 'awebooking' ); ?></th>
					<td><?php abrs_price( $the_booking->get( 'paid' ), $the_booking->get( 'currency' ) ); ?></td>
				</tr>

				<tr>
					<th><?php echo esc_html__( 'Balance Due:', 'awebooking' ); ?></th>
					<td><?php abrs_price( $the_booking->get( 'balance_due' ), $the_booking->get( 'currency' ) ); ?></td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="booking-section">
		<?php if ( $the_booking->is_editable() ) : ?>

			<a class="button abrs-button" href="<?php echo esc_url( abrs_admin_route( '/booking-room', [ 'refer' => $the_booking->get_id() ] ) ); ?>">
				<span><?php esc_html_e( 'Add room', 'awebooking' ); ?></span>
			</a>

			<a class="button abrs-button" href="<?php echo esc_url( abrs_admin_route( '/booking-service', [ 'refer' => $the_booking->get_id() ] ) ); ?>">
				<span><?php esc_html_e( 'Add service', 'awebooking' ); ?></span>
			</a>

		<?php else : ?>

			<span class="abrs-label tippy" title="<?php esc_html_e( 'Change the booking status back to "Pending" to edit this', 'awebooking' ); ?>"><?php esc_html_e( 'This booking is no longer editable', 'awebooking' ); ?></span>

		<?php endif; ?>
	</div>
</div>
