<?php

use AweBooking\Constants;
use AweBooking\Support\WP_Data;

$hotels = WP_Data::get( 'posts', [
	'post_type'      => Constants::HOTEL_LOCATION,
	'post_status'    => 'publish',
	'order'          => 'ASC',
	'orderby'        => 'menu_order',
	'posts_per_page' => 500,
]);

?>
<ul class="abrs-sortable" id="js-sorting-hotels">
	<?php foreach ( $hotels as $key => $name ) : ?>
		<li class="abrs-sortable__item">
			<input type="hidden" name="list_hotels_order[]" value="<?php echo esc_attr( $key ); ?>">

			<div class="abrs-sortable__head">
				<span class="abrs-sortable__handle"></span>
			</div>

			<div class="abrs-sortable__body">
				<span><?php echo esc_html( $name ); ?></span>
			</div>

			<div class="abrs-sortable__actions">

			</div>
		</li>

	<?php endforeach ?>
</ul><!-- /.abrs-sortable -->

<script type="text/javascript">
(function($) {
	'use strict';

	$(function() {
		Sortable.create($('#js-sorting-hotels')[0], {
			handle: '.abrs-sortable__handle',
			animation: 150,
		});
	});

})(jQuery);
</script>
