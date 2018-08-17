<?php

/* @var $the_booking \AweBooking\Model\Booking */
global $the_booking;

$fee_items = $the_booking->get_fees();

if ( abrs_blank( $fee_items ) ) {
	return;
}

?>

<table class="awebooking-table abrs-booking-rooms widefat fixed">
	<thead>
		<tr>
			<th style="width: 80%;"><?php echo esc_html__( 'Fee', 'awebooking' ); ?></th>
			<th class="abrs-text-right"><span><?php esc_html_e( 'Price', 'awebooking' ); ?></span></th>
		</tr>
	</thead>

	<tbody>
		<?php foreach ( $fee_items as $fee ) : ?>
			<tr>
				<td><?php echo esc_html( $fee->get_name() ); ?></td>
				<td class="abrs-text-right"><?php abrs_price( $fee->get( 'total' ), $the_booking->get( 'currency' ) ); ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table><!-- /.awebooking-table -->
