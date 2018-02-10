<?php
/** @vars $source, $store */

?><tr>
	<td>
		<div class="cmb2-onoffswitch default">
			<input type="checkbox" class="cmb2-onoffswitch-checkbox" name="sources[<?php echo esc_attr( $source['uid'] ); ?>][enabled]" id="reservation_<?php echo esc_attr( $source['uid'] ); ?>" value="on" <?php echo $source['enabled'] ? 'checked="true"' : ''; ?> <?php echo ( 'direct_website' === $source['uid'] ) ? 'disabled="true"' : ''; ?>>
			<label class="cmb2-onoffswitch-label" for="reservation_<?php echo esc_attr( $source['uid'] ); ?>"></label>
		</div>
	</td>

	<td>
		<strong><?php echo esc_html( $source['name'] ); ?></strong>
		<div class="row-actions afloat-right">
			<span class="edit"><a href="<?php echo esc_url( awebooking( 'url' )->admin_route( "source/{$source['uid']}" ) ); ?>"><?php esc_html_e( 'Edit', 'awebooking' ); ?></a> | </span>
		</div>
	</td>
	<?php var_dump($source) ?>
	<td>
		<?php if ( isset( $source['tax'] ) && $source['tax'] ) : ?>
			<?php esc_html_e( 'Tax or Fee:', 'awebooking' ); ?> <?php echo esc_html( $source['tax'] ); ?>
		<?php else : ?>
			<i><?php esc_html_e( 'No tax or fee', 'awebooking' ); ?></i>
		<?php endif; ?>
	</td>

</tr>
