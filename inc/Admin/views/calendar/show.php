<div class="wrap">
	<h1 class="wp-heading-inline"><?php printf( esc_html__( 'Calendar &quot;%s&quot;', 'awebooking' ), esc_html( $room_type->get_title() ) ); ?></h1>
	<hr class="wp-header-end">

	<?php $scheduler->display(); ?>
</div>
