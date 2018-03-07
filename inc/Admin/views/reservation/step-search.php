<div class="wrap awebooking-reservation-page">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'New Reservation', 'awebooking' ); ?></h1>
	<hr class="wp-header-end">

	<div class="awebooking-toolbar">
		<?php $this->partial( 'reservation/search-form.php', compact( 'reservation' ) ); ?>
	</div>

	<div class="awebooking-reservation__container">

		<div class="awebooking-reservation__main">
			<form method="POST" action="<?php echo esc_url( awebooking( 'url' )->admin_route( '/reservation/add_item' ) ); ?>">
				<?php wp_nonce_field( 'awebooking_add_room', '_wpnonce', true ); ?>

				<input type="hidden" name="check_in" value="<?php // echo esc_attr( $stay->get_check_in()->toDateString() ); ?>">
				<input type="hidden" name="check_out" value="<?php // echo esc_attr( $stay->get_check_out()->toDateString() ); ?>">

<!-- 				<div class="tablenav">
					<div class="alignleft actions">
						<span><?php echo esc_html__( 'Searching for:', 'awebooking' ); ?></span>
						<strong><?php //printf( _n( '%s night', '%s nights', $stay->nights(), 'awebooking' ), esc_html( $stay->nights() ) ); // @codingStandardsIgnoreLine ?></strong>,
						<span><?php //echo wp_kses_post( $stay->as_string() ); ?></span>
					</div>
				</div> -->

				<?php $availability_table->display(); ?>
			</form>
		</div>

		<div class="awebooking-reservation__aside">
			<?php $this->partial( 'reservation/aside-reservation.php', compact( 'reservation' ) ); ?>
		</div>

	</div><!-- /.awebooking__reservation-container -->
</div><!-- /.wrap -->
