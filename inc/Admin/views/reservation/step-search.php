<div class="wrap awebooking-reservation-page">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'New Reservation', 'awebooking' ); ?></h1>
	<hr class="wp-header-end">

	<div class="awebooking-toolbar">
		<?php $this->partial( 'reservation/search-form.php', compact( 'reservation' ) ); ?>
	</div>

	<div class="awebooking-reservation__container">

		<div class="awebooking-reservation__main">
			<form method="POST" action="<?php echo esc_url( awebooking( 'url' )->admin_route( '/reservation' ) ); ?>">
				<?php wp_nonce_field( 'add_roomstay', '_wpnonce', true ); ?>

				<input type="hidden" name="check_in" value="<?php // echo esc_attr( $timespan->get_start_date()->toDateString() ); ?>">
				<input type="hidden" name="check_out" value="<?php // echo esc_attr( $timespan->get_end_date()->toDateString() ); ?>">

<!-- 				<div class="tablenav">
					<div class="alignleft actions">
						<span><?php echo esc_html__( 'Searching for:', 'awebooking' ); ?></span>
						<strong><?php //printf( _n( '%s night', '%s nights', $timespan->nights(), 'awebooking' ), esc_html( $timespan->nights() ) ); // @codingStandardsIgnoreLine ?></strong>,
						<span><?php //echo wp_kses_post( $timespan->as_string() ); ?></span>
					</div>
				</div> -->

				<?php $availability_table->display(); ?>
			</form>
		</div>

		<div class="awebooking-reservation__aside">
			<?php // $this->partial( 'reservation/aside-reservation.php', compact( 'reservation' ) ); ?>
		</div>

	</div><!-- /.awebooking__reservation-container -->
</div><!-- /.wrap -->
