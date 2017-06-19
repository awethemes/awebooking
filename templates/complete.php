<?php if ( isset( $_GET['step'] ) && $_GET['step'] === 'complete' && ! empty( $_COOKIE['awebooking-booking-id'] ) ) : ?>
	<p><?php echo sprintf( esc_html__( 'Thanks for your booking. Your booking ID: #%s', 'awebooking' ), $_COOKIE['awebooking-booking-id'] ); ?></p>
	<?php return; ?>
<?php endif ?>
