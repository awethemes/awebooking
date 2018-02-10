<?php
/* @vars $request, $booking, $controls */

?><div class="wrap" style="max-width: 1200px;">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Add Room', 'awebooking' ); ?></h1>
	<a class="page-title-action" href="<?php echo esc_url( $booking->get_edit_url() ); ?>"><?php esc_html_e( 'Booking reference', 'awebooking' ); ?> #<?php echo esc_html( $booking->get_id() ); ?></a>

	<hr class="wp-header-end">

	<form method="GET" action="" class="awebooking-reservation__searching-from" >
		<input type="hidden" name="awebooking" value="<?php echo esc_attr( $request->route_path() ); ?>">

		<?php $controls->output(); ?>

	</form><!-- /.awebooking-reservation__searching-from -->

	<?php if ( isset( $availability_table ) ) : ?>

		<form method="POST" action="<?php echo esc_url( awebooking( 'url' )->admin_route( "booking/{$booking->get_id()}/room" ) ); ?>">
			<?php wp_nonce_field( 'add_booking_room', '_wpnonce' ); ?>

			<?php $availability_table->display(); ?>
		</form>

	<?php endif; ?>

</div><!-- /.wrap -->
