<?php
/* @vars $booking */

$booked_services = $booking->get_services();

$services_selection = abrs_services_for_reservation( [
	'exclude' => $booked_services->pluck( 'id' )->all()
],
 	[],
 	[
		'nights'     => 0,
		'base_price' => 0,
	]
);

?>

<div class="wrap">
	<h1 class="wp-heading-inline screen-reader-text"><?php esc_html_e( 'Add Service', 'awebooking' ); ?></h1>
	<hr class="wp-header-end">

	<div class="abrs-card abrs-card--page abrs-booking-services">
		<form method="POST" action="<?php echo esc_url( abrs_admin_route( 'booking-service' ) ); ?>">
			<input type="hidden" name="_refer" value="<?php echo esc_attr( $booking->get_id() ); ?>">

			<?php wp_nonce_field( 'create_booking_service' ); ?>

			<div class="abrs-card__header">
				<h2 class=""><?php esc_html_e( 'Add Service', 'awebooking' ); ?></h2>
				<span><?php esc_html_e( 'Reference', 'awebooking' ); ?> <a href="<?php echo esc_url( get_edit_post_link( $booking->get_id() ) ); ?>">#<?php echo esc_html( $booking->get_booking_number() ); ?></a></span>
			</div>

			<div class="cmb2-wrap awebooking-wrap abrs-card__body">
				<ul class="abrs-sortable">
					<?php foreach ( $services_selection as $service_selection ) : ?>

						<?php abrs_admin_template_part( 'booking/html-service-item.php', compact( 'service_selection', 'booked_services' ) ); ?>

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

<style type="text/css">
	.abrs-booking-services .abrs-sortable__order {
		width: 60px;
	}

	.abrs-booking-services .abrs-sortable__order input[type="number"] {
		width: 50px;
	}
</style>
