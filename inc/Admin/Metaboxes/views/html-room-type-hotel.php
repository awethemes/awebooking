<?php

$room_type = abrs_get_room_type( $post->ID );

?>

<div class="submitbox" id="hotel_location">
	<select name="_hotel_id" style="width: 100%;">
		<?php foreach ( $hotels as $id => $hotel ) : ?>
			<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $room_type ? $room_type->get( 'hotel_id' ) : 0, $id ); ?>><?php echo esc_html( $hotel ); ?></option>
		<?php endforeach; ?>
	</select>
</div>
