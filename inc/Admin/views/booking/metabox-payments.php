<?php
/* @vars $the_booking */

use AweBooking\Support\Formatting as F;

global $the_booking;

dd( $the_booking );

$payment_items = $the_booking->get_payments();

?><table class="awebooking-table widefat fixed striped">
		<thead>
			<tr>
				<th style="width: 200px;"><span><?php esc_html_e( 'Payment', 'awebooking' ); ?></span></th>
				<th><span class="screen-reader-text"><?php esc_html_e( 'Comment', 'awebooking' ); ?></span></th>
				<th style="width: 100px;"></th>
				<th style="width: 150px;"><span><?php esc_html_e( 'Date & time', 'awebooking' ); ?></span></th>
				<th class="atext-right" style="width: 100px;"><span><?php esc_html_e( 'Amount', 'awebooking' ); ?></span></th>
			</tr>
		</thead>

		<tbody>
			<?php if ( $payment_items->isEmpty() ) : ?>
				<td colspan="5">
					<p style="text-align: center; margin: 1em 0; color: #676767;">No payments found</p>
				</td>
			<?php else : ?>
				<?php foreach ( $payment_items as $payment_item ) : ?>
					<tr>
						<td>
							<strong><?php echo esc_html( $payment_item->get_method_title() ); ?></strong>
						</td>

						<td>
							<div class="awebooking-contents">
								<?php if ( $payment_item['comment'] ) : ?>
									<?php echo wp_kses_post( wpautop( wptexturize( $payment_item['comment'] ) ) ); ?>
								<?php endif ?>
							</div>
						</td>

						<td>
							<div class="row-actions">
								<span class="edit"><a href="<?php echo esc_url( $payment_item->get_edit_link() ); ?>"><?php echo esc_html__( 'Edit', 'awebooking' ); ?></a> | </span>
								<span class="trash"><a href="<?php echo esc_url( $payment_item->get_delete_link() ); ?>" data-method="awebooking-delete" class="submitdelete">Delete</a></span>
							</div>
						</td>

						<td>
							<?php echo esc_html( $payment_item->get_date_paid()->to_wp_datetime_string() ); ?>
						</td>

						<td class="atext-right">
							<span class="awebooking-label"><?php echo esc_html( $payment_item['amount'] ); ?></span>
						</td>
					</tr>
				<?php endforeach ?>
			<?php endif ?>
		</tbody>

		<tfoot>
			<tr>
				<td colspan="3">
					<a href="<?php echo esc_url( awebooking( 'url' )->admin_route( "/booking/{$the_booking->get_id()}/payment/create" ) ); ?>" class="button button-primary"><?php esc_html_e( 'Register payment', 'awebooking' ); ?></a>
				</td>

				<th colspan="2" style="width: 250px;">
					<strong><?php esc_html_e( 'Balance Due', 'awebooking' ); ?></strong>
					<span class="afloat-right awebooking-label awebooking-label--danger"><?php echo F::money( $the_booking->get_balance_due() ); ?></span>
				</th>
			</tr>
		</tfoot>
	</table>
</div><!-- #awebooking-booking-payments -->
