<?php

use AweBooking\Money\Money;
use AweBooking\Support\Utils as U;

$room_items = $the_booking->get_line_items();

?><div id="awebooking-booking-rooms" style="margin-top: 1em;">
	<table class="awebooking-table widefat fixed striped">
		<thead>
			<tr>
				<th style="width: 250px;"><?php echo esc_html__( 'Room', 'awebooking' ); ?></th>
				<th style="width: 250px;"><?php echo esc_html__( 'Stay', 'awebooking' ); ?></th>
				<th width=""><?php echo esc_html__( 'Guest', 'awebooking' ); ?></th>
				<th style="width: 150px;"></th>
				<th class="atext-right" style="width: 100px;"><span><?php esc_html_e( 'Amount', 'awebooking' ); ?></span></th>
			</tr>
		</thead>

		<tbody>
			<?php if ( $room_items->isEmpty() ) : ?>

				<tr>
					<td colspan="5">
						<p class="awebooking-no-items"><?php esc_html_e( 'No rooms found', 'awebooking' ); ?></p>
					</td>
				</tr>

			<?php else : ?>

				<?php foreach ( $room_items as $room_item ) : ?>

					<tr>
						<td><?php $room_item->print_label(); ?></td>

						<td><?php print U::optional( $room_item->get_stay() )->as_string(); // WPCS: XSS OK. ?></td>

						<td><?php print U::optional( $room_item->get_guest() )->as_string(); // WPCS: XSS OK. ?></td>

						<td style="text-align: right;">
							<?php if ( $the_booking->is_editable() ) : ?>
								<div class="row-actions">
									<span class="edit"><a href="<?php echo esc_url( $room_item->get_edit_link() ); ?>"><?php esc_html_e( 'Edit', 'awebooking' ); ?></a> | </span>
									<span class="trash"><a href="<?php echo esc_url( $room_item->get_delete_link() ); ?>" data-method="awebooking-delete"><?php esc_html_e( 'Delete', 'awebooking' ); ?></a></span>
								</div>
							<?php endif; ?>
						</td>

						<td style="text-align: right;">
							<span class="awebooking-label"><?php $the_booking->format_money( $room_item->get_total() ); ?></span>
						</td>
					</tr>
				<?php endforeach; ?>

			<?php endif ?>
		</tbody>

		<tfoot>
			<tr>
				<td colspan="3">
					<?php if ( $the_booking->is_editable() ) : ?>

						<a href="<?php echo esc_url( awebooking( 'url' )->admin_route( "/booking/{$the_booking->get_id()}/room/add" ) ); ?>" class="button">
							<span><?php esc_html_e( 'Add room', 'awebooking' ); ?></span>
						</a>

					<?php else : ?>

						<span class="awebooking-label awebooking-label--square" title="<?php esc_html_e( 'Change the booking status to "Pending" to edit this.', 'awebooking' ); ?>"><?php esc_html_e( 'This booking is no longer editable.', 'awebooking' ); ?></span>

					<?php endif ?>
				</td>

				<th colspan="2" style="width: 250px;">
					<strong><?php esc_html_e( 'Total', 'awebooking' ); ?></strong>
					<span class="afloat-right awebooking-label awebooking-label--info"><?php echo Money::of( $the_booking->get_total(), $the_booking->get_currency() ); // WPCS: XSS OK. ?></span>
				</th>
			</tr>
		</tfoot>

	</table><!-- /.awebooking-table -->
</div><!-- /#awebooking-booking-rooms -->
