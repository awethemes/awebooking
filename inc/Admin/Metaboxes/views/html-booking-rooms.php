<?php
/* @var $the_booking */

// List the room items.
$room_items = $the_booking->get_items( 'line_item' );

?>
<style type="text/css">
	#awebooking-booking-rooms .hndle,
	#awebooking-booking-rooms .handlediv { display:none }
</style>

<table class="awebooking-table widefat fixed striped">
	<thead>
		<tr>
			<th style="width: 250px;"><?php echo esc_html__( 'Room', 'awebooking' ); ?></th>
			<th style="width: 250px;"><?php echo esc_html__( 'Nights', 'awebooking' ); ?></th>
			<th style="width: 250px;"><?php echo esc_html__( 'Stay', 'awebooking' ); ?></th>
			<th width=""><?php echo esc_html__( 'Guest', 'awebooking' ); ?></th>
			<th style="width: 150px;"></th>
			<th class="atext-right" style="width: 100px;"><span><?php esc_html_e( 'Amount', 'awebooking' ); ?></span></th>
		</tr>
	</thead>

	<tbody>
		<?php if ( abrs_blank( $room_items ) ) : ?>

			<tr>
				<td colspan="6">
					<p class="awebooking-no-items"><?php esc_html_e( 'No rooms found', 'awebooking' ); ?></p>
				</td>
			</tr>

		<?php else : ?>
			<?php foreach ( $room_items as $room_item ) : ?>

				<tr>
					<td><?php echo esc_html( $room_item['name'] ); ?></td>

					<td><?php echo $room_item->get_nights_stayed(); ?></td>

					<td><?php print abrs_optional( $room_item->get_timespan() )->as_string(); // WPCS: XSS OK. ?></td>

					<td><?php print abrs_optional( $room_item->get_guest() )->as_string(); // WPCS: XSS OK. ?></td>

					<td style="text-align: right;">
						<?php if ( $the_booking->is_editable() ) : ?>
							<?php $action_link = abrs_admin_route( '/booking-room/' . $room_item->get_id() ); ?>

							<div class="row-actions">
								<span class="edit"><a href="<?php echo esc_url( $room_item->get_edit_link() ); ?>"><?php esc_html_e( 'Edit', 'awebooking' ); ?></a> | </span>
								<span class="trash"><a href="<?php echo esc_url( wp_nonce_url( $action_link, 'delete_room_' . $room_item->get_id() ) ); ?>" data-method="abrs-delete"><?php esc_html_e( 'Delete', 'awebooking' ); ?></a></span>
							</div>
						<?php endif; ?>
					</td>

					<td style="text-align: right;">
						<span class="awebooking-label"><?php abrs_price( $room_item->get_total() ); ?></span>
					</td>
				</tr>

			<?php endforeach; ?>
		<?php endif ?>
	</tbody>

	<tfoot>
		<tr>
			<td colspan="4">
				<?php if ( $the_booking->is_editable() ) : ?>

					<a class="button abrs-button" href="<?php echo esc_url( abrs_admin_route( '/booking-room', [ 'refer' => $the_booking->get_id() ] ) ); ?>">
						<span><?php esc_html_e( 'Add room', 'awebooking' ); ?></span>
					</a>

				<?php else : ?>

					<span class="awebooking-label awebooking-label--square" title="<?php esc_html_e( 'Change the booking status to "Pending" to edit this.', 'awebooking' ); ?>"><?php esc_html_e( 'This booking is no longer editable.', 'awebooking' ); ?></span>

				<?php endif ?>
			</td>

			<th colspan="2">
				<strong><?php esc_html_e( 'Total', 'awebooking' ); ?></strong>
				<span class="afloat-right awebooking-label awebooking-label--info"><?php abrs_price( $the_booking->get_total(), $the_booking->get_currency() ); // WPCS: XSS OK. ?></span>
			</th>
		</tr>
	</tfoot>

</table><!-- /.awebooking-table -->
