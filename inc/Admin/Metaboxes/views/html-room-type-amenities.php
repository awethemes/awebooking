<div class="awebooking-panel abwrap" id="room-type-amenities" style="display: none;">
	<?php
	post_categories_meta_box( get_post(), [
		'args' => [ 'taxonomy' => 'hotel_amenity' ],
	]);
	?>
</div>
