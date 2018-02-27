<?php

$paid = $booking->get_paid();
$balance_due = $booking->get_balance_due();

?><div style="text-align: left; min-width: 250px;">
	<h2><?php printf( esc_html__( 'Booking #%1$d', 'awebooking' ), $booking->get_id() ); ?></h2>

	<p><?php echo esc_html( $booking->get_customer_name() ); ?></p>
	<p><?php echo esc_html( $booking->get_status() ); ?></p>

	<p>
		<strong><?php echo esc_html__( 'Total:', 'awebooking' ); ?></strong>
		<span><?php $booking->format_money( $booking->get_total() ); ?></span>
	</p>

	<p>
		<?php if ( $paid->is_zero() ) : ?>
			<strong class="awebooking-label awebooking-label--square awebooking-label--warning"><?php echo esc_html__( 'Not Paid', 'awebooking' ); ?></strong>
		<?php elseif ( $balance_due->is_positive() ) : ?>
			<strong class="awebooking-label awebooking-label--square awebooking-label--info"><?php echo esc_html__( 'Not Paid Full', 'awebooking' ); ?></strong>
		<?php elseif ( $balance_due->is_zero() ) : ?>
			<strong class="awebooking-label awebooking-label--square awebooking-label--success"><?php echo esc_html__( 'Paid', 'awebooking' ); ?></strong>
		<?php endif ?>
	</p>
</div>
