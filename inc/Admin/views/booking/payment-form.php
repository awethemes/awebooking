<?php
/* @vars $booking, $form_builder, $payment_item */

$old_input = awebooking( 'session' )->get_old_input();
if ( ! empty( $old_input ) ) {
	$form_builder->fill( $old_input );
}

$action_link = $payment_item->exists()
	? abrs_admin_route( "booking-payment/{$payment_item->get_id()}" )
	: abrs_admin_route( 'booking-payment' );

?><div class="wrap">
	<h1 class="wp-heading-inline screen-reader-text"><?php $payment_item->exists() ? esc_html_e( 'Update Payment', 'awebooking' ) : esc_html_e( 'Add Payment', 'awebooking' ); ?></h1>
	<hr class="wp-header-end">

	<div class="abrs-card abrs-card--page">
		<form method="POST" action="<?php echo esc_url( $action_link ); ?>">
			<input type="hidden" name="_refer" value="<?php echo esc_attr( $booking->get_id() ); ?>">

			<?php if ( $payment_item->exists() ) : ?>
				<input type="hidden" name="_method" value="PUT">
				<?php wp_nonce_field( 'update_payment_' . $payment_item->get_id() ); ?>
			<?php else : ?>
				<?php wp_nonce_field( 'create_booking_payment' ); ?>
			<?php endif ?>

			<div class="abrs-card__header">
				<h2 class=""><?php $payment_item->exists() ? esc_html_e( 'Update Payment', 'awebooking' ) : esc_html_e( 'Add Payment', 'awebooking' ); ?></h2>
				<span><?php esc_html_e( 'Reference', 'awebooking' ); ?> <a href="<?php echo esc_url( get_edit_post_link( $booking->get_id() ) ); ?>">#<?php echo esc_html( $booking->get_booking_number() ); ?></a></span>
			</div>

			<div class="cmb2-wrap awebooking-wrap abrs-card__body">
				<div class="cmb2-metabox">
					<div class="cmb-row">
						<div class="cmb-th"><label><?php echo esc_html__( 'Total charge', 'awebooking' ); ?></label></div>

						<div class="cmb-td">
							<span class="abrs-label"><?php abrs_price( $booking->get_total() ); // WPCS: XSS OK. ?></span>
						</div>
					</div>

					<div class="cmb-row">
						<div class="cmb-th"><label><?php echo esc_html__( 'Already paid', 'awebooking' ); ?></label></div>

						<div class="cmb-td">
							<span class="abrs-label abrs-label--success"><?php abrs_price( $booking->get_paid() ); // WPCS: XSS OK. ?></span>
						</div>
					</div>

					<div class="cmb-row">
						<div class="cmb-th"><label><?php echo esc_html__( 'Balance Due', 'awebooking' ); ?></label></div>

						<div class="cmb-td">
							<span class="abrs-label abrs-label--warning"><?php abrs_price( $booking->get_balance_due() ); // WPCS: XSS OK. ?></span>
						</div>
					</div>

					<?php
					// Print the fields.
					foreach ( $form_builder->prop( 'fields' ) as $field ) {
						$form_builder->render_field( $field );
					}
					?>
				</div>
			</div>

			<div class="abrs-card__footer submit abrs-text-right">
				<a href="<?php echo esc_url( get_edit_post_link( $booking->get_id() ) ); ?>" class="button button-link"><?php echo esc_html__( 'Cancel', 'awebooking' ); ?></a>
				<button type="submit" class="button abrs-button"><?php echo esc_html__( 'Save', 'awebooking' ); ?></button>
			</div>
		</form>

	</div>
</div><!-- /.wrap -->
