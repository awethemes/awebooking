<?php
/* @vars $calr, $event, $calendar, $scheduler, $attributes */

// Get the booking ID.
$booking = $event->get_value();

// This mean we have some days in state is marked as have booking
// but missing in boooking data. Need some way to resolve this.
if ( ! $the_booking = abrs_get_booking( $booking ) ) {
	return; // TODO: ...
}

// Ignore cancelled booking.
if ( 'awebooking-cancelled' === $the_booking['status'] ) {
	return;
}

// Add the booking status in the wrap class.
$attributes['class'] .= ' ' . $the_booking->get( 'status' );

?><div <?php echo abrs_html_attributes( $attributes ); // WPCS: XSS OK. ?>>
	<a class="scheduler-inline-text" href="<?php echo esc_url( get_edit_post_link( $the_booking->get_id() ) ); ?>" target="_blank">
		<span class="">#<?php echo esc_html( $the_booking->get_booking_number() ); ?></span>
		<span style="display: inline-block; margin: 0 2px;">&middot;</span>
		<span><?php echo esc_html( $the_booking->get_formatted_guest_name() ); ?></span>
	</a>

	<div style="display: none;">
		<div class="js-tippy-html abrs-ptb1">
			<span><?php echo esc_html__( 'Booking Summary', 'awebooking' ); ?></span> &middot;
		</div>
	</div>
</div>
