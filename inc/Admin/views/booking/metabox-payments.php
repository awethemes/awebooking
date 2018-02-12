<?php

use AweBooking\Support\Utils as U;

$payment_items = $the_booking->get_payments();
$balance_due   = $the_booking->get_balance_due();

?><div id="awebooking-booking-payments" style="margin-top: 20px;">

	<table class="awebooking-table widefat fixed striped">
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
					<p class="awebooking-no-items"><?php esc_html_e( 'No payments found', 'awebooking' ); ?></p>
				</td>
			<?php else : ?>
				<?php foreach ( $payment_items as $payment_item ) : ?>
					<tr>
						<td>
							<strong><?php echo esc_html( $payment_item->get_method_title() ); ?></strong>

							<?php if ( $payment_item->is_deposit() ) : ?>
								<sup><?php echo esc_html_x( 'Deposit', 'deposit label', 'awebooking' ); ?></sup>
							<?php endif ?>

							<?php if ( $comment = $payment_item->get_comment() ) : ?>
								<a href="#payment_comment_<?php echo esc_attr( $payment_item->get_id() ); ?>" class="afloat-right" data-toggle="awebooking-popup" data-placement="right"><span class="dashicons dashicons-info"></span></a>

								<div id="payment_comment_<?php echo esc_attr( $payment_item->get_id() ); ?>" style="display: none;">
									<div class="awebooking-dialog-contents" style="padding: 0 1em;"><?php echo wp_kses_post( wpautop( wptexturize( $payment_item['comment'] ) ) ); ?></div>
								</div>
							<?php endif ?>
						</td>

						<td>
							<div class="awebooking-contents">
								<?php
								if ( $gateway = $payment_item->resolve_gateway() ) {
									U::optional( $gateway )->display_payment_contents( $payment_item, $the_booking );
								}

								do_action( "awebooking/booking/display_payment_{$payment_item->get_method()}", $payment_item, $the_booking );

								do_action( 'awebooking/booking/display_payment', $payment_item, $the_booking );
								?>
							</div>
						</td>

						<td>
							<div class="row-actions">
								<span class="edit"><a href="<?php echo esc_url( $payment_item->get_edit_link() ); ?>"><?php echo esc_html__( 'Edit', 'awebooking' ); ?></a> | </span>
								<span class="trash"><a href="<?php echo esc_url( $payment_item->get_delete_link() ); ?>" data-method="awebooking-delete" class="submitdelete"><?php esc_html_e( 'Delete', 'awebooking' ); ?></a></span>
							</div>
						</td>

						<td><?php echo esc_html( $payment_item->get_date_paid()->to_wp_datetime_string() ); ?></td>

						<td class="atext-right">
							<span class="awebooking-label"><?php $the_booking->format_money( $payment_item->get_amount() ); ?></span>
						</td>
					</tr>
				<?php endforeach ?>
			<?php endif ?>
		</tbody>

		<tfoot>
			<tr>
				<td colspan="3">
					<a href="<?php echo esc_url( awebooking( 'url' )->admin_route( "/booking/{$the_booking->get_id()}/payment/create" ) ); ?>" class="button">
						<span><?php esc_html_e( 'Register payment', 'awebooking' ); ?></span>
					</a>
				</td>

				<th colspan="2" style="width: 250px;">
					<strong><?php esc_html_e( 'Balance Due', 'awebooking' ); ?></strong>
					<span class="afloat-right awebooking-label awebooking-label--<?php echo $balance_due->is_zero() ? 'success' : 'danger'; ?>"><?php $the_booking->format_money( $balance_due ); ?></span>
				</th>
			</tr>
		</tfoot>
	</table>

</div><!-- #awebooking-booking-payments -->
