<?php

use AweBooking\Admin\Forms\Search_Reservation_Form;

$controls = new Search_Reservation_Form;
if ( isset( $reservation ) ) {
	$controls->with_reservation( $reservation );
}

?><form class="awebooking-reservation__searching-from" method="GET" action="<?php echo esc_url( awebooking( 'url' )->admin_route( '/reservation/create' ) ); ?>">
	<input type="hidden" name="awebooking" value="/reservation/create">
	<input type="hidden" name="step" value="search">

	<?php $controls->output(); ?>

</form><!-- /.awebooking-reservation__searching-from -->
