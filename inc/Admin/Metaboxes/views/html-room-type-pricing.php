<div class="awebooking-panel abwrap" id="room-type-pricing" style="display: none;">
	<div class="abrow">
		<div class="abcol-3 abcol-sm-12">
			<?php $form->show_field( 'base_price' ); ?>
		</div>

		<?php if ( abrs_tax_enabled() && ( 'per_room' === abrs_get_tax_rate_model() ) ) : ?>
			<div class="abcol-3 abcol-sm-12">
				<?php $form->show_field( '_tax_rate_id' ); ?>
			</div>
		<?php endif; ?>
	</div>

	<div class="abrs-postbox-title">
		<h3><?php esc_html_e( 'Restrictions', 'awebooking' ); ?></h3>
		<p><?php esc_html_e( 'Limit the availability of the rate.', 'awebooking' ); ?></p>
	</div>

	<div class="abrow">
		<div class="abcol-3 abcol-sm-12">
			<?php $form->show_field( 'minimum_night' ); ?>
		</div>

		<div class="abcol-3 abcol-sm-12">
			<?php $form->show_field( '_rate_maximum_los' ); ?>
		</div>
	</div>

	<div class="abrs-postbox-title">
		<h3><?php esc_html_e( 'Inclusions', 'awebooking' ); ?></h3>
	</div>

	<?php
	global $the_room_type;

	$ids = $the_room_type->get( 'rate_services' );

	$services = abrs_list_services( [ 'post__in' => $ids ?: [ 0 ] ] )
		->pluck( 'name', 'id' )
		->all();
	?>

	<div class="abrow">
		<div class="abcol-8 abcol-sm-12">
			<label class="block-label" for="rate_services"><?php echo esc_html__( 'Include services', 'awebooking' ); ?></label>

			<select id="rate_services" name="_rate_services[]" class="selectize-search-services" multiple="multiple">
				<?php foreach ( $services as $id => $name ) : ?>
					<option value="<?php echo absint( $id ); ?>" <?php selected( in_array( $id, $ids ) ); ?>><?php echo esc_html( $name ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>

	<div class="abrow">
		<div class="abcol-8 abcol-sm-12">
			<?php $form->show_field( '_rate_inclusions' ); ?>
		</div>
	</div>

	<div class="abrs-postbox-title">
		<h3><?php esc_html_e( 'Policies', 'awebooking' ); ?></h3>
	</div>

	<div class="abrow">
		<div class="abcol-10 abcol-sm-12">
			<?php $form->show_field( '_rate_policies' ); ?>
		</div>
	</div>
</div><!-- /.awebooking-panel -->
