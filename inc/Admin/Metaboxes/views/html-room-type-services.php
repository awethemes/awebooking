<?php
global $the_room_type;

$ids = $the_room_type->get( 'rate_services' );
$services = abrs_list_services( [ 'post__in' => $ids ?: [ 0 ] ] )
	->pluck( 'name', 'id' )
	->all();
?>

<select id="rate_services" name="rate_services[]" class="selectize-search-services" multiple="multiple">
	<?php foreach ( $services as $id => $name ) : ?>
		<option value="<?php echo absint( $id ); ?>" <?php selected( in_array( $id, $ids ) ); ?>><?php echo esc_html( $name ); ?></option>
	<?php endforeach; ?>
</select>
