<?php

$controls['amount']->set_value( $booking->get_balance_due() );

$old_input = awebooking( 'session' )->get_old_input();
if ( ! empty( $old_input ) ) {
	$controls->fill( $old_input );
}

?><div class="wrap" style="max-width: 1200px;">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Register payment', 'awebooking' ); ?></h1>
	<hr class="wp-header-end">

	<form method="POST" action="<?php echo esc_url( awebooking( 'url' )->admin_route( "booking/{$booking->get_id()}/payment" ) ); ?>">
		<?php wp_nonce_field( 'create_booking_payment' ); ?>

		<?php $controls->output(); ?>

		<div>
			<button type="submit" class="button button-primary"><?php echo esc_html__( 'Save', 'awebooking' ); ?></button>
			<a href="<?php echo esc_url( $booking->get_edit_url() ); ?>" class="button"><?php echo esc_html__( 'Cancel', 'awebooking' ); ?></a>
		</div>

	</form>
</div>
