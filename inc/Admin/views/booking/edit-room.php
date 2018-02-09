<?php
/* @vars $request, $booking, $controls */

?><div class="wrap" style="max-width: 1200px;">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Edit Room', 'awebooking' ); ?></h1>
	<br><span><?php esc_html_e( 'Booking reference:', 'awebooking' ); ?> <a href="<?php echo esc_url( $booking->get_edit_url() ); ?>" style="text-decoration: none;">#<?php echo esc_html( $booking->get_id() ); ?></a></span>

	<hr class="clear">

	<form method="POST" action="<?php echo esc_url( $room_item->get_permalink( 'update' ) ); ?>">
		<?php wp_nonce_field( 'update_booking_room_' . $room_item->get_id() ); ?>

		<?php $controls->output(); ?>

		<div class="awebooking-form-actions">
			<button type="submit" class="button button-primary"><?php echo esc_html__( 'Save', 'awebooking' ); ?></button>
			<a href="<?php echo esc_url( $booking->get_edit_url() ); ?>" class="button"><?php echo esc_html__( 'Cancel', 'awebooking' ); ?></a>
		</div>

		<input type="hidden" name="_method" value="PUT">
	</form>
</div><!-- /.wrap -->
