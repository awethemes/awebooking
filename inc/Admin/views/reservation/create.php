<?php

// use AweBooking\Calendar\Finder\Finder;
// use AweBooking\Calendar\Resource\Resource;
// use AweBooking\Calendar\Provider\State_Provider;
// use AweBooking\Calendar\Provider\Cached_Provider;
// use AweBooking\Calendar\Period\Month;

// $resources = [
// 	new Resource( 1 ),
// 	new Resource( 2 ),
// ];

// $month = new Month( 2018, 03 );

// $finder = new Finder( $resources, new Cached_Provider( new State_Provider( $resources ) ) );
// dump( $finder->only( 1 )->find( $month ) );

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
