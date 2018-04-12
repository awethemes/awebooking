<div class="awebooking-panel abwrap" id="room-type-services" style="display: none;">
	<?php
	post_categories_meta_box( get_post(), [
		'args' => [ 'taxonomy' => 'hotel_extra_service' ],
	]);
	?>
</div>
