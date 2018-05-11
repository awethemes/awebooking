<style type="text/css">
	#awebooking-booking-data .hndle,
	#awebooking-booking-data .handlediv,
	#post-body-content, #titlediv { display:none }
	#awebooking-booking-data.closed .inside { display: block !important; }
</style>

<div class="awebooking-wrap abwrap cmb2-wrap">
	<div class="cmb2-metabox cmb2-inline-metabox">
		<input name="post_title" type="hidden" value="<?php echo empty( $post->post_title ) ? esc_html__( 'Booking', 'awebooking' ) : esc_attr( $post->post_title ); ?>" />
		<input name="post_status" type="hidden" value="<?php echo esc_attr( $post->post_status ); ?>" />

		<div class="booking-heading">
			<h2><?php printf( esc_html__( 'Booking #%1$s', 'awebooking' ), esc_html( $the_booking->get_booking_number() ) ); // @codingStandardsIgnoreLine ?></h2>
		</div>

		<div class="abrow abrs-mb1">
			<div class="abcol-4 abcol-sm-12">
				<h3><?php esc_html_e( 'General', 'awebooking' ); ?></h3>

				<?php
				// Print fields.
				$form->show_field( '_date_created' );

				$form->show_field( '_source' );

				$form->show_field( '_status' );

				$form->show_field( '_customer_id' );

				if ( $_customer = $the_booking['customer_id'] ) {
					echo '<div class="clear abrs-profile-links">';

					$_args = [ 'post_type' => 'awebooking', '_customer' => $_customer ]; // @codingStandardsIgnoreLine
					printf( '<a href="%s" target="_blank">%s</a>', esc_url( add_query_arg( $_args, admin_url( 'edit.php' ) ) ), ' ' . esc_html__( 'View other bookings &rarr;', 'awebooking' ) );
					printf( '<a href="%s" target="_blank" style="float: right;">%s</a>', esc_url( add_query_arg( 'user_id', $_customer, admin_url( 'user-edit.php' ) ) ), ' ' . esc_html__( 'Profile &rarr;', 'awebooking' ) );

					echo '</div><!-- /.abrs-profile-links -->';
				}
				?>
			</div>

			<div class="js-booking-column abcol-4 abcol-sm-12">
				<h3>
					<?php esc_html_e( 'Summary', 'awebooking' ); ?>
					<a href="#" class="button-editnow js-editnow tippy" title="<?php esc_html_e( 'Edit', 'awebooking' ); ?>"><span class="dashicons dashicons-edit"></span></a>
				</h3>

				<div class="reservation-summary-block">
					<?php if ( $the_booking['currency'] ) : ?>
						<p>
							<strong><?php esc_html_e( 'Currency', 'awebooking' ); ?>:</strong>
							<span class="abrs-badge tippy" title="<?php echo esc_attr( abrs_currency_name( $the_booking['currency'] ) ); ?>"><?php echo esc_html( $the_booking['currency'] ); ?></span>
						</p>
					<?php endif ?>
				</div>

				<div class="js-booking-data reservation-summary-block">
					<p>
						<strong><?php esc_html_e( 'Estimated time of arrival', 'awebooking' ); ?>:</strong>
						<span class="abrs-badge js-editnow" title="<?php esc_html_e( 'Edit', 'awebooking' ); ?>"><?php echo abrs_fluent( abrs_list_hours() )->get( $the_booking['arrival_time'], esc_html__( 'N/A', 'awebooking' ) ); // WPCS: XSS OK. ?></span>
					</p>

					<p>
						<strong><?php esc_html_e( 'Special requests', 'awebooking' ); ?>:</strong>
						<?php echo ( ! $the_booking['customer_note'] ) ? '<span class="abrs-badge js-editnow" data-focus="#excerpt" title="' . esc_html__( 'Edit', 'awebooking' ) . '">' . esc_html__( 'None', 'awebooking' ) . '</span>' : ''; ?>
					</p>

					<?php if ( $the_booking['customer_note'] ) : ?>
						<div class="customer_note"><?php echo wp_kses_post( wptexturize( wpautop( $the_booking->get( 'customer_note' ) ) ) ); ?></div>
					<?php endif ?>
				</div>

				<div class="js-edit-booking-data" style="display: none;">
					<?php $form->show_field( '_arrival_time' ); ?>

					<?php $form->show_field( 'excerpt' ); ?>
				</div>

				<div class="reservation-details">
					<p class="night-stay">
						<?php if ( ( $nights_stay = $the_booking->get( 'nights_stay' ) ) == -1 ) : ?>
							<?php echo 'Length of stay varies, see each room.'; ?>
						<?php else : ?>
							<strong><span class="afc afc-moon" style="vertical-align: middle;"></span> <?php echo esc_html( $nights_stay ); ?> night stay</strong>
						<?php endif ?>
					</p>

					<div class="abrow no-gutters">
						<div class="abcol-6">
							<p>
								<strong><?php esc_html_e( 'Check-in', 'awebooking' ); ?></strong>
								<span><?php echo esc_html( abrs_format_date( $the_booking->get( 'check_in_date' ) ) ); ?></span>
							</p>
						</div>

						<div class="abcol-6">
							<p>
								<strong><?php esc_html_e( 'Check-in', 'awebooking' ); ?></strong>
								<span><?php echo esc_html( abrs_format_date( $the_booking->get( 'check_out_date' ) ) ); ?></span>
							</p>
						</div>
					</div>
				</div>
			</div>

			<div class="js-booking-column abcol-4 abcol-sm-12">
				<h3>
					<?php esc_html_e( 'Customer Details', 'awebooking' ); ?>
					<a href="#" class="button-editnow js-editnow tippy" title="<?php esc_html_e( 'Edit', 'awebooking' ); ?>"><span class="dashicons dashicons-edit"></span></a>
				</h3>

				<div class="js-booking-data">
					<!-- // -->
				</div>

				<div class="js-edit-booking-data" style="display: none;">
					<div class="abrow">
						<?php foreach ( $form->sections['customer']['fields'] as $field => $args ) : ?>
							<div class="abrs-mb1 abcol-sm-12 <?php echo ( isset( $args['col-half'] ) && $args['col-half'] ) ? 'abcol-6' : 'abcol-12'; ?>">
								<?php $form->show_field( $field ); ?>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>

	</div><!-- /.cmb2-metabox -->
</div><!-- /.awebooking-wrap -->
