<?php
use AweBooking\Model\Service;

/* @vars $booking */

$old_input = awebooking( 'session' )->get_old_input();
if ( ! empty( $old_input ) ) {
	$form_builder->fill( $old_input );
}

$action_link = abrs_admin_route( 'booking-service' );

$services = abrs_list_services();

if ( $services->isEmpty() ) {
	return;
}

$operations = abrs_get_service_operations();

$services_exist = $booking->get_services();
$service_ids = $services_exist->pluck( 'service_id' )->all();
?>

<div class="wrap">
	<h1 class="wp-heading-inline screen-reader-text"><?php esc_html_e( 'Add Service', 'awebooking' ); ?></h1>
	<hr class="wp-header-end">

	<div class="abrs-card abrs-card--page">
		<form method="POST" action="<?php echo esc_url( $action_link ); ?>">
			<input type="hidden" name="_refer" value="<?php echo esc_attr( $booking->get_id() ); ?>">

			<?php wp_nonce_field( 'create_booking_service' ); ?>

			<div class="abrs-card__header">
				<h2 class=""><?php esc_html_e( 'Add Service', 'awebooking' ); ?></h2>
				<span><?php esc_html_e( 'Reference', 'awebooking' ); ?> <a href="<?php echo esc_url( get_edit_post_link( $booking->get_id() ) ); ?>">#<?php echo esc_html( $booking->get_booking_number() ); ?></a></span>
			</div>

			<div class="cmb2-wrap awebooking-wrap abrs-card__body">
				<ul class="abrs-sortable">
					<?php foreach ( $services as $service ) : ?>
						<li class="abrs-sortable__item">
							<div class="abrs-sortable__head">
								<span class="abrs-sortable__order">
									<input type="checkbox" name="list_services[]" value="<?php echo esc_attr( $service->get_id() ); ?>" <?php checked( in_array( $service->get_id(), $service_ids ) ); ?>>
								</span>
							</div>

							<div class="abrs-sortable__body">
								<span><?php echo esc_html( $service->get( 'name' ) ); ?></span>
							</div>

							<div class="abrs-sortable__actions">
								<?php if ( isset( $operations[ $service->get( 'operation' ) ] ) ) : ?>
									<span class="abrs-badge"><?php print abrs_format_service_price( $service->get( 'value' ), $service->get( 'operation' ) ); // WPCS: xss ok. ?></span>
								<?php endif ?>
							</div>
						</li>

					<?php endforeach ?>
				</ul><!-- /.abrs-sortable -->

			</div>

			<div class="abrs-card__footer submit abrs-text-right">
				<a href="<?php echo esc_url( get_edit_post_link( $booking->get_id() ) ); ?>" class="button button-link"><?php echo esc_html__( 'Cancel', 'awebooking' ); ?></a>
				<button type="submit" class="button abrs-button"><?php echo esc_html__( 'Save', 'awebooking' ); ?></button>
			</div>
		</form>

	</div>
</div><!-- /.wrap -->
