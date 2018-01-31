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
	</td>

	<td>
		<i>No tax or fee</i>
	</td>

	<td>
		<input type="hidden" name="sources[<?php echo esc_attr( $source['uid'] ); ?>][uid]" value="<?php echo esc_attr( $source['uid'] ); ?>">
	</td>
</tr>
