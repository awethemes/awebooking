<?php

use AweBooking\Admin\Forms\Search_Reservation_Form;

?><form class="awebooking-reservation__searching-from" method="GET" action="<?php echo esc_url( awebooking( 'url' )->admin_route( '/reservation/create' ) ); ?>">
	<input type="hidden" name="awebooking" value="/reservation/create">
	<input type="hidden" name="step" value="search">

	<?php ( new Search_Reservation_Form )->output(); ?>
</form><!-- /.awebooking-reservation__searching-from -->
