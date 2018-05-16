<div class="submitbox" id="hotel_location">
	<select name="parent_id" style="width: 100%;">
		<option value="0">
			<?php
			/* translators: %s default hotel */
			printf( esc_html_x( '%s (Default)', 'default hotel', 'awebooking' ), esc_html( abrs_get_option( 'hotel_name' ) ) );
			?>
		</option>

		<?php foreach ( $hotels as $id => $hotel ) { ?>
			<option value="<?php echo esc_attr( $id ); ?>" <?php selected( wp_get_post_parent_id( $post->ID ), $id ) ?>><?php echo esc_html( $hotel ); ?></option>
		<?php } ?>
	</select>
</div>
