

	<div class="abrs-postbox-title">
		<h3><?php esc_html_e( 'Restrictions', 'awebooking' ); ?></h3>
		<p><?php esc_html_e( 'Limit the availability of the rate.', 'awebooking' ); ?></p>
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
