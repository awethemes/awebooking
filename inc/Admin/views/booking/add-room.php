<?php

use AweBooking\Support\WP_Data;

$controls = abrs_create_form( 'search-rooms' );

$selected_dates = [];

if ( $request->filled( 'check-in', 'check-out' ) ) {
	$selected_dates = array_values( $request->only( 'check-in', 'check-out' ) );
}

?>

<div class="wrap">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Add Room', 'awebooking' ); ?></h1>
	<span><?php esc_html_e( 'Reference', 'awebooking' ); ?> <a href="<?php echo esc_url( get_edit_post_link( $booking->get_id() ) ); ?>">#<?php echo esc_html( $booking->get_booking_number() ); ?></a></span>
	<hr class="wp-header-end"/>

	<div class="abrs-card abrs-card--page" style="width: 910px;">
		<div class="abrs-card__header">
			<form method="GET" action="<?php echo esc_url( abrs_admin_route( '/booking-room' ) ); ?>">
				<input type="hidden" name="awebooking" value="/booking-room">
				<input type="hidden" name="refer" value="<?php echo esc_attr( $booking->get_id() ); ?>">

				<div class="dp-flex cmb2-inline-metabox">
					<?php
					if ( abrs_multiple_hotels() ) {
						$controls->show_field( [
							'id'               => 'hotel',
							'type'             => 'select',
							'options_cb'       => WP_Data::cb( 'pages', [ 'post_type' => 'hotel_location' ] ),
							'sanitization_cb'  => 'absint',
							'show_option_none' => false,
							'default'          => $request->get( 'hotel' ),
							'show_names'       => false,
						] );
					}

					$controls->show_field( [
						'id'         => 'date',
						'type'       => 'abrs_dates',
						'default'    => $selected_dates,
						'show_names' => false,
						'classes'    => 'abrs-mlr1',
					] );
					?>

					<div class="abrs-space"></div>
					<button class="button abrs-button" type="submit"><span class="dashicons dashicons-search"></span><?php esc_html_e( 'Query', 'awebooking' ); ?></button>
				</div>
			</form>
		</div>

		<div class="abrs-card__body" style="padding: 0;">
			<?php if ( ! isset( $results ) ) : ?>

				<p class="awebooking-no-items"><?php esc_html_e( 'Enter dates to search rooms', 'awebooking' ); ?></p>

			<?php elseif ( isset( $results ) && ! $results->has_items() ) : ?>

				<p class="awebooking-no-items"><?php esc_html_e( 'Sorry, no room found', 'awebooking' ); ?></p>

			<?php else : ?>
				<form method="POST" action="<?php echo esc_url( abrs_admin_route( '/booking-room' ) ); ?>">
					<?php wp_nonce_field( 'add_booking_room', '_wpnonce' ); ?>

					<input type="hidden" name="_refer" value="<?php echo esc_attr( $booking->get_id() ); ?>">
					<input type="hidden" name="check_in" value="<?php echo esc_attr( $res_request['check_in'] ); ?>">
					<input type="hidden" name="check_out" value="<?php echo esc_attr( $res_request['check_out'] ); ?>">

					<?php if ( isset( $results ) ) : ?>
						<table class="widefat fixed striped availability-table">
							<thead>
							<tr>
								<th style="width: 38px;"><span class="screen-reader-text"><?php echo esc_html__( 'Image', 'awebooking' ); ?></span></th>
								<th style="width: 20%;"><?php echo esc_html__( 'Room Type', 'awebooking' ); ?></th>
								<th style="width: 15%;"><?php echo esc_html__( 'Assign Room', 'awebooking' ); ?></th>
								<th style="width: 30%;"><?php echo esc_html__( 'Occupancy', 'awebooking' ); ?></th>
								<th><?php echo esc_html__( 'Price', 'awebooking' ); ?></th>
							</tr>
							</thead>
							<tbody>
							<?php foreach ( $results as $avai ) : ?>
								<?php abrs_admin_template()->partial( 'booking/html-avai-row.php', compact( 'avai', 'res_request' ) ); ?>
							<?php endforeach ?>
							</tbody>
						</table>
					<?php endif ?>

				</form>
			<?php endif ?>
		</div>

	</div>
</div>
