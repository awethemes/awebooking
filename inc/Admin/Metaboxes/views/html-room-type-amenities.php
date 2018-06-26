<div class="awebooking-panel abwrap" id="room-type-amenities" style="display: none;">
	<div class="abrs-postbox-title"><h3><?php esc_html_e( 'Amenities', 'awebooking' ); ?></h3></div>

	<?php
	post_categories_meta_box( get_post(), [
		'args' => [ 'taxonomy' => 'hotel_amenity' ],
	]);
	?>

	<div class="abrs-postbox-title"><h3><?php esc_html_e( 'Extra Informations', 'awebooking' ); ?></h3></div>

	<div class="abrow">
		<div class="abcol-4">
			<?php $form->show_field( '_area_size' ); ?>
		</div>

		<div class="abcol-4">
			<?php $form->show_field( '_room_view' ); ?>
		</div>
	</div>

	<div class="abrow">
		<div class="abcol-12">
			<?php $form->show_field( '_beds' ); ?>
		</div>
	</div>
</div>
