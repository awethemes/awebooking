<?php

/* @var $the_booking \AweBooking\Model\Booking */
global $the_booking;

$columns = 9;
if ( ! abrs_children_bookable() ) {
	$columns--;
}

if ( ! abrs_infants_bookable() ) {
	$columns--;
}

// List the items.
$room_items = $the_booking->get_rooms();

?>

<table class="awebooking-table abrs-booking-rooms widefat fixed">
	<thead>
	<tr>
		<th style="width: 20%;"><?php echo esc_html__( 'Room Type', 'awebooking' ); ?></th>
		<th style="width: 15%;"><?php echo esc_html__( 'Rate Plan', 'awebooking' ); ?></th>
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
			<td colspan="<?php echo esc_attr( $columns ); ?>">
				<p class="awebooking-no-items"><?php esc_html_e( 'No rooms', 'awebooking' ); ?></p>
			</td>
		</tr>

	<?php else : ?>

		<?php foreach ( $room_items as $item ) : ?>
			<?php
			/* @var $item \AweBooking\Model\Booking\Room_Item */
			$timespan    = $item->get_timespan();
			$room        = abrs_get_room( $item->get( 'room_id' ) );
			$room_type   = abrs_get_room_type( $item->get( 'room_type_id' ) );
			$rate_plan   = abrs_get_rate( $item->get( 'rate_plan_id' ) );
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

					<div>
						<strong class="row-title"><?php echo esc_html( $room ? $room->get( 'name' ) : $item->get_name() ); ?></strong>
						<span class="dp-block"><?php echo esc_html( $room_type ? $room_type->get( 'title' ) : '' ); ?></span>
					</div>
				</td>

				<td>
					<strong><?php echo esc_html( $rate_plan ? $rate_plan->get_name() : esc_html__( '-', 'awebooking' ) ); ?></strong>
				</td>

				<td>
					<span class="abrs-badge"><?php echo esc_html( abrs_optional( $timespan )->get_nights() ); ?></span>
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
							<span class="swap"><a href="<?php echo esc_url( add_query_arg( 'action', 'swap', $action_link ) ); ?>"><?php esc_html_e( 'Swap', 'awebooking' ); ?></a> | </span>
							<span class="trash"><a href="<?php echo esc_url( wp_nonce_url( $action_link, "delete_room_{$item->get_id()}" ) ); ?>" data-method="abrs-delete"><?php esc_html_e( 'Delete', 'awebooking' ); ?></a></span>
						</div>

					<?php endif; ?>

					<?php esc_html_e( 'Subtotal:', 'awebooking' ); ?> <?php abrs_price( $item->get( 'subtotal' ), $the_booking->get( 'currency' ) ); ?>
					<br>
					<?php esc_html_e( 'Total:', 'awebooking' ); ?> <?php abrs_price( $item->get( 'total' ), $the_booking->get( 'currency' ) ); ?>
					<br>
					<?php esc_html_e( 'TAX:', 'awebooking' ); ?> <?php abrs_price( $item->get( 'total_tax' ) ); ?>
				</td>
			</tr>

		<?php endforeach; ?>
	<?php endif; ?>

	</tbody>
</table><!-- /.awebooking-table -->
