<div class="wrap awebooking-reservation-page">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'New Reservation', 'awebooking' ); ?></h1>
	<hr class="wp-header-end">

	<div class="awebooking-toolbar">
		<?php $this->partial( 'reservation/search-form.php' ); ?>
	</div>

	<div class="awebooking-reservation__container">
		<?php $this->partial( 'reservation/placeholder.php' ); ?>
	</div><!-- /.awebooking__reservation-container -->
</div><!-- /.wrap -->
