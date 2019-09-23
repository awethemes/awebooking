<?php

/* @var $the_booking \AweBooking\Model\Booking */
global $the_booking;

$columns = 11;
if ( ! abrs_children_bookable() ) {
	$columns--;
}

if ( ! abrs_infants_bookable() ) {
	$columns--;
}

// List the items.
$room_items = $the_booking->get_rooms();

?>

<style>
	.abrs-inline-table tbody th,
	.abrs-inline-table tbody td {
		padding: 0;
		border: none !important;
	}

	.price-area {
		background: #f9f9f9;
	}

	th.price-area {
		color: #696969 !important;
		font-size: 10px !important;
		font-weight: 500;
		text-transform: uppercase;
	}

	td.price-area {
		color: #111 !important;
	}
</style>

<table class="awebooking-table abrs-booking-rooms widefat fixed">
	<thead>
		<tr>
			<th><?php echo esc_html__( 'Room Type', 'awebooking' ); ?></th>
			<th><?php echo esc_html__( 'Rate Plan', 'awebooking' ); ?></th>
			<th><?php echo esc_html__( 'Stay', 'awebooking' ); ?></th>
			<th style="width: 7%;"><?php echo esc_html__( 'Adults', 'awebooking' ); ?></th>

			<?php if ( abrs_children_bookable() ) : ?>
				<th style="width: 7%;"><?php echo esc_html__( 'Children', 'awebooking' ); ?></th>
			<?php endif ?>

			<?php if ( abrs_infants_bookable() ) : ?>
				<th style="width: 7%;"><?php echo esc_html__( 'Infants', 'awebooking' ); ?></th>
			<?php endif ?>

			<th class="abrs-text-right price-area" style="width: 80px;">
				<span><?php esc_html_e( 'Price', 'awebooking' ); ?></span>
			</th>

			<th class="abrs-text-right price-area" style="width: 40px;">
				<span><?php esc_html_e( 'Qty', 'awebooking' ); ?></span>
			</th>

			<th class="abrs-text-right price-area" style="width: 80px;">
				<span><?php esc_html_e( 'Subtotal', 'awebooking' ); ?></span>
			</th>

			<th class="abrs-text-right price-area" style="width: 60px;">
				<span><?php esc_html_e( 'TAX', 'awebooking' ); ?></span>
			</th>

			<th class="abrs-text-right price-area" style="width: 80px;">
				<span><?php esc_html_e( 'Total', 'awebooking' ); ?></span>
			</th>
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

					printf( '<div class="abrs-thumb-image abrs-fleft" style="margin-right: 10px;">%1$s</div>', $thumbnail ); // @wpcs: XSS OK.
					?>

					<div>
						<strong class="row-title"><?php echo esc_html( $room ? $room->get( 'name' ) : $item->get_name() ); ?></strong>
						<span class="dp-block"><?php echo esc_html( $room_type ? $room_type->get( 'title' ) : '' ); ?></span>
					</div>

					<?php if ( $the_booking->is_editable() ) : ?>
						<div class="row-actions abrs-fleft">
							<span class="edit"><a href="<?php echo esc_url( $action_link ); ?>"><?php esc_html_e( 'Edit', 'awebooking' ); ?></a> | </span>
							<span class="swap"><a href="<?php echo esc_url( add_query_arg( 'action', 'swap', $action_link ) ); ?>"><?php esc_html_e( 'Swap', 'awebooking' ); ?></a> | </span>
							<span class="trash"><a href="<?php echo esc_url( wp_nonce_url( $action_link, "delete_room_{$item->get_id()}" ) ); ?>" data-method="abrs-delete"><?php esc_html_e( 'Delete', 'awebooking' ); ?></a></span>
						</div>
					<?php endif; ?>
				</td>

				<td>
					<strong><?php echo esc_html( $rate_plan ? $rate_plan->get_name() : esc_html__( '-', 'awebooking' ) ); ?></strong>
				</td>

				<td>
					<p>
						<?php if ($timespan !== null) : ?>
							<?php echo esc_html( abrs_format_date( $timespan->get_start_date() ) ); ?>
						<?php endif ?>

						<span>-</span>

						<?php if ($timespan !== null) : ?>
							<?php echo esc_html( abrs_format_date( $timespan->get_end_date() ) ); ?>
						<?php endif ?>
					</p>

					<span>
						<span class=""><?php echo esc_html( abrs_optional( $timespan )->get_nights() ); ?></span>
						<span class=""><?php echo esc_html__( 'night(s)', 'awebooking' ); ?></span>
					</span>
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

				<td class="abrs-text-right price-area">
					<?php abrs_price( $item->get( 'subtotal' ), $the_booking->get( 'currency' ) ); ?>
				</td>

				<td class="abrs-text-right price-area">
					<span class="abrs-badge">1</span>
				</td>

				<td class="abrs-text-right price-area">
					<?php abrs_price( $item->get( 'subtotal' ), $the_booking->get( 'currency' ) ); ?>
				</td>

				<td class="abrs-text-right price-area">
					<?php abrs_price( $item->get( 'total_tax' ), $the_booking->get( 'currency' ) ); ?>
				</td>

				<td class="abrs-text-right price-area">
					<strong><?php abrs_price( $item->get( 'total' ), $the_booking->get( 'currency' ) ); ?></strong>
				</td>
			</tr>

		<?php endforeach; ?>
	<?php endif; ?>

	</tbody>
</table><!-- /.awebooking-table -->
