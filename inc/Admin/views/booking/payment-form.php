<?php
/* @vars $booking, $controls */

$is_updateform = isset( $payment_item ) && 'edit_payment_item' === get_current_screen()->action;

$old_input = awebooking( 'session' )->get_old_input();
if ( ! empty( $old_input ) ) {
	$controls->fill( $old_input );
}

$action_link = $is_updateform
	? awebooking( 'url' )->admin_route( "booking/{$booking->get_id()}/payment/{$payment_item->get_id()}" )
	: awebooking( 'url' )->admin_route( "booking/{$booking->get_id()}/payment" );

?><div class="wrap" style="max-width: 1200px;">
	<h1 class="wp-heading-inline"><?php $is_updateform ? esc_html_e( 'Update payment', 'awebooking' ) : esc_html_e( 'Register payment', 'awebooking' ); ?></h1>
	<br><span><?php esc_html_e( 'Booking reference:', 'awebooking' ); ?> <a href="<?php echo esc_url( $booking->get_edit_url() ); ?>" style="text-decoration: none;">#<?php echo esc_html( $booking->get_id() ); ?></a></span>

	<hr class="clear">

	<form method="POST" action="<?php echo esc_url( $action_link ); ?>">
		<?php wp_nonce_field( $is_updateform ? 'update_booking_payment_' . $payment_item->get_id() : 'create_booking_payment' ); ?>

		<?php if ( $is_updateform ) : ?>
			<input type="hidden" name="_method" value="PUT">
		<?php endif ?>

		<?php $controls->output(); ?>

		<div class="awebooking-form-actions">
			<button type="submit" class="button button-primary"><?php echo esc_html__( 'Save', 'awebooking' ); ?></button>
			<a href="<?php echo esc_url( $booking->get_edit_url() ); ?>" class="button"><?php echo esc_html__( 'Cancel', 'awebooking' ); ?></a>
		</div>

	</form>
</div><!-- /.wrap -->
