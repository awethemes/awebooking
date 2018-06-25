<?php

$hotels = abrs_list_hotels();

if ( $hotels->isEmpty() ) {
	return;
}
?>

<ul class="abrs-sortable" id="js-sorting-hotels">
	<?php foreach ( $hotels as $hotel ) : ?>
		<li class="abrs-sortable__item">
			<input type="hidden" name="list_hotels_order[]" value="<?php echo esc_attr( $hotel->get_id() ); ?>">

			<div class="abrs-sortable__head">
				<span class="abrs-sortable__handle"></span>
			</div>

			<div class="abrs-sortable__body">
				<span><?php echo esc_html( $hotel->get_name() ); ?></span>
			</div>

			<div class="abrs-sortable__actions">
				<a href="<?php echo esc_url( get_edit_post_link( $hotel->get_id() ) ); ?>" title="<?php echo esc_html( $hotel->get_name() ); ?>"><span class="dashicons dashicons-edit"></span></a>
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
