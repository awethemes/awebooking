<?php
/* @var $the_booking */

// List the room items.
$room_items = $the_booking->get_rooms();

?><style type="text/css">
	#awebooking-booking-rooms .hndle,
	#awebooking-booking-rooms .handlediv { display:none }
	#awebooking-booking-data.closed .inside { display: block !important; }
</style>

<table class="awebooking-table widefat fixed striped">
	<thead>
		<tr>
			<th style="width: 20%;"><?php echo esc_html__( 'Room Type', 'awebooking' ); ?></th>
			<th style="width: 15%;"><?php echo esc_html__( 'Rate Plan', 'awebooking' ); ?></th>
			<th style="width: 5%;"><span class="afc afc-moon tippy" title="<?php echo esc_html__( 'Nights', 'awebooking' ); ?>"></span><span class="screen-reader-text"><?php echo esc_html__( 'Nights', 'awebooking' ); ?></span></th>
			<th style="width: 7%;"><?php echo esc_html__( 'Adults', 'awebooking' ); ?></th>
			<th style="width: 10%;"><?php echo esc_html__( 'Check In', 'awebooking' ); ?></th>
			<th style="width: 10%;"><?php echo esc_html__( 'Check Out', 'awebooking' ); ?></th>
			<?php if ( abrs_children_bookable() ) : ?>
				<th style="width: 7%;"><?php echo esc_html__( 'Children', 'awebooking' ); ?></th>
			<?php endif ?>

			<?php if ( abrs_infants_bookable() ) : ?>
				<th style="width: 7%;"><?php echo esc_html__( 'Infants', 'awebooking' ); ?></th>
			<?php endif ?>

			<th><span><?php esc_html_e( 'Price', 'awebooking' ); ?></span></th>
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

						<strong class="row-title">Room Type</strong>
						<span class="dp-block"><?php echo esc_html( $item->get_name() ); ?></span>
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
							<?php echo esc_html( abrs_format_date( $timespan->get_start_date() ) ); ?>
						<?php endif ?>
					</td>

					<td>
						<?php if ( ! is_null( $timespan ) ) : ?>
							<?php echo esc_html( abrs_format_date( $timespan->get_end_date() ) ); ?>
						<?php endif ?>
					</td>

					<td>
						<?php echo esc_html( number_format_i18n( $item->get( 'adults' ) ) ); ?>
					</td>

					<td>
						<?php echo esc_html( $item['children'] ? number_format_i18n( $item->get( 'children' ) ) : '-' ); ?>
					</td>

					<td>
						<?php echo esc_html( $item['infants'] ? number_format_i18n( $item->get( 'infants' ) ) : '-' ); ?>
					</td>

					<td>
						<?php abrs_price( $item->get( 'total' ), $the_booking->get( 'currency' ) ); ?>

						<?php if ( $the_booking->is_editable() ) : ?>
							<div class="row-actions abrs-fright">
								<span class="edit"><a href="<?php echo esc_url( $action_link ); ?>"><?php esc_html_e( 'Edit', 'awebooking' ); ?></a> | </span>
								<span class="trash"><a href="<?php echo esc_url( wp_nonce_url( $action_link, "delete_room_{$item->get_id()}" ) ); ?>" data-method="abrs-delete"><?php esc_html_e( 'Delete', 'awebooking' ); ?></a></span>
							</div>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif ?>
	</tbody>

	<tbody>
		<tr>
			<td colspan="9">

				<strong><?php esc_html_e( 'Total', 'awebooking' ); ?></strong>
				<span class="abrs-fright abrs-label abrs-label--info"><?php abrs_price( $the_booking->get_total(), $the_booking->get( 'currency' ) ); // WPCS: XSS OK. ?></span>

			</td>
		</tr>
	</tbody>

	<tfoot>
		<tr>
			<td colspan="9">
				<?php if ( $the_booking->is_editable() ) : ?>

					<a class="button abrs-button" href="<?php echo esc_url( abrs_admin_route( '/booking-room', [ 'refer' => $the_booking->get_id() ] ) ); ?>">
						<span><?php esc_html_e( 'Add room', 'awebooking' ); ?></span>
					</a>

				<?php else : ?>

					<span class="abrs-label tippy" title="<?php esc_html_e( 'Change the booking status back to "Pending" to edit this', 'awebooking' ); ?>"><?php esc_html_e( 'This booking is no longer editable', 'awebooking' ); ?></span>

				<?php endif ?>
			</td>
		</tr>
	</tfoot>
</table><!-- /.awebooking-table -->
