<?php

use AweBooking\Constants;
use AweBooking\Model\Room_Type;

$r_query = new WP_Query( [
	'post_type'      => Constants::ROOM_TYPE,
	'post_status'    => 'publish',
	'no_found_rows'  => true,
	'posts_per_page' => 250,
] );

$room_types = abrs_collect( $r_query->posts )
	->map_into( Room_Type::class )
	->reject( function ( Room_Type $r ) {
		return count( $r->get_rooms() ) === 0;
	} )->values();
?>

<div class="wrap">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Calendar', 'awebooking' ); ?></h1>
	<hr class="wp-header-end">

	<div id="awebooking-calendar-root"></div>
</div>

<script>
	window.awebookingRoomTypes = <?php echo $room_types->to_json() ?: '[]'; ?>
</script>
