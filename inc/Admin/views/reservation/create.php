<?php

use AweBooking\Model\Stay;
use AweBooking\Model\Room_Type;
use AweBooking\Reservation\Pricing\Room_Rate;

$room_type = new Room_Type( 83 );
$stay = new Stay( '2017-03-05', '2017-03-10' );

$room_rate = new Room_Rate( $stay, $room_type );
dd( $room_rate );

?>
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
