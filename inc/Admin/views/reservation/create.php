<div class="wrap awebooking-reservation-page">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'New Reservation', 'awebooking' ); ?></h1>
	<hr class="wp-header-end">

	<div class="awebooking-toolbar">
		<?php $this->partial( 'reservation/search-form.php' ); ?>
	</div>

	<div class="awebooking-reservation__container">

		<div class="awebooking-reservation__placeholder">
			<span class="dashicons dashicons-calendar"></span>
			<p><?php esc_html_e( 'Check-In and Check-out dates to search for availability', 'awebooking' ); ?></p>
		</div>

	</div><!-- /.awebooking__reservation-container -->
</div><!-- /.wrap -->
