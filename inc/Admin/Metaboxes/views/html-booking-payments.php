<?php

$payment_items = $the_booking->get_payments()
	->sortByDesc( function ( $e ) {
		return $e->is_deposit();
	});

?>
<style type="text/css">
	#awebooking-booking-payments .hndle,
	#awebooking-booking-payments .handlediv { display:none; }
	#awebooking-booking-payments.closed .inside { display: block !important; }
</style>

<table class="awebooking-table widefat fixed striped">
	<thead>
		<tr>
			<th style="width: 20%;"><span><?php esc_html_e( 'Payment', 'awebooking' ); ?></span></th>
			<th style="width: 45%;"><span class="screen-reader-text"><?php esc_html_e( 'Payment Data', 'awebooking' ); ?></span></th>
			<th style="width: 10%;"></th>
			<th style="width: 15%;"><span><?php esc_html_e( 'Created at', 'awebooking' ); ?></span></th>
			<th class="abrs-text-right" style="width: 10%;"><span><?php esc_html_e( 'Amount', 'awebooking' ); ?></span></th>
		</tr>
	</thead>

	<tbody>
		<?php if ( abrs_blank( $payment_items ) ) : ?>
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

						<?php if ( $comment = $payment_item['comment'] ) : ?>
							<span class="abrs-fright tippy" data-tippy-interactive="true" data-tippy-size="large" data-tippy-html="#private_payment_comment_<?php echo esc_attr( $payment_item->get_id() ); ?>">
								<span class="screen-reader-text"><?php esc_html_e( 'Payment comment', 'awebooking' ); ?></span>
								<span class="dashicons dashicons-admin-comments"></span>
							</span>

							<div id="private_payment_comment_<?php echo esc_attr( $payment_item->get_id() ); ?>" style="display: none;">
								<div class="" style="min-width: 250px;"><?php echo wp_kses_post( wptexturize( wpautop( $payment_item->get( 'comment' ) ) ) ); ?></div>
							</div>
						<?php endif ?>
					</td>

					<td>
						<div class="awebooking-contents">
							<?php
							do_action( "abrs_display_payment_{$payment_item->get( 'method' )}", $payment_item, $the_booking );

							do_action( 'abrs_display_payment', $payment_item, $the_booking );
							?>
						</div>
					</td>

					<td>
						<div class="row-actions">
							<?php $action_link = abrs_admin_route( '/booking-payment/' . $payment_item->get_id() ); ?>
							<span class="edit"><a href="<?php echo esc_url( $action_link ); ?>"><?php echo esc_html__( 'Edit', 'awebooking' ); ?></a> | </span>
							<span class="trash"><a href="<?php echo esc_url( rawurldecode( wp_nonce_url( $action_link, 'delete_payment_' . $payment_item->get_id() ) ) ); ?>" data-method="abrs-delete" class="submitdelete"><?php esc_html_e( 'Delete', 'awebooking' ); ?></a></span>
						</div>
					</td>

					<td>
						<?php
						if ( $payment_item['date_paid'] ) {
							printf( '<abbr title="%1$s" class="tippy">%2$s</abbr>',
								esc_html( abrs_format_date_time( $payment_item->get( 'date_paid' ) ) ),
								esc_html( abrs_format_date_time( $payment_item->get( 'date_paid' ), abrs_get_date_format() ) )
							);
						}
						?>
					</td>

					<td class="abrs-text-right">
						<span class="abrs-label"><?php abrs_price( $payment_item->get( 'amount' ), $the_booking->get( 'currency' ) ); // WPCS: XSS OK. ?></span>
					</td>
				</tr>
			<?php endforeach ?>
		<?php endif ?>
	</tbody>

	<tfoot>
		<tr>
			<td colspan="3">
				<a class="button abrs-button" href="<?php echo esc_url( add_query_arg( 'refer', $the_booking->get_id(), abrs_admin_route( '/booking-payment' ) ) ); ?>">
					<span><?php esc_html_e( 'Register payment', 'awebooking' ); ?></span>
				</a>
			</td>

			<th colspan="2">
				<strong><?php esc_html_e( 'Balance Due', 'awebooking' ); ?></strong>
				<span class="abrs-fright awebooking-label awebooking-label--<?php echo empty( $the_booking['balance_due'] ) ? 'success' : 'danger'; ?>"><?php abrs_price( $the_booking->get( 'balance_due' ), $the_booking->get( 'currency' ) ); // WPCS: XSS OK. ?></span>
			</th>
		</tr>
	</tfoot>
</table>
