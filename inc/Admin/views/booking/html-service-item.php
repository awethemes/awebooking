<?php
/* @vars $service_selection, $service_data */

$input_prefix = 'services[' . $service->get_id() . ']';
?>

<li class="abrs-sortable__item">
	<div class="abrs-sortable__head">
		<span class="abrs-sortable__order">
			<input type="hidden" name="<?php echo esc_attr( $input_prefix ); ?>[id]" value="<?php echo esc_attr( $service->get_id() ); ?>">

			<?php if ( $service->is_quantity_selectable() ) : ?>
				<input type="number" data-bind="value: quantity" min="0" class="form-input form-input--quantity" value="<?php echo absint( $service_data['quantity'] ); ?>" name="<?php echo esc_attr( $input_prefix ); ?>[quantity]">

			<?php else : ?>

				<div class="nice-checkbox">
					<input type="checkbox" class="form-input--quantity" data-bind="value: quantity" id="service_id_<?php echo esc_attr( $service->get_id() ); ?>" name="<?php echo esc_attr( $input_prefix ); ?>[quantity]" value="1" <?php checked( 1, absint( $service_data['quantity'] ) ); ?> />
				</div>

			<?php endif; ?>
		</span>
	</div>

	<div class="abrs-sortable__body">
		<a href="<?php echo esc_url( get_edit_post_link( $service->get_id() ) ); ?>" title="<?php echo esc_attr( $service->get( 'name' ) ); ?>" target="_blank">
			<strong><?php echo esc_html( $service->get( 'name' ) ); ?></strong>
		</a>

		<span class="sup-placeholder">
			<?php print abrs_format_service_price( $service->get( 'amount' ), $service->get( 'operation' ) ); // WPCS: xss ok. ?>
		</span>
	</div>

	<div class="abrs-sortable__actions">
		<?php if ( $service->is_quantity_selectable() ) : ?>
			<span data-bind="text: quantity"></span>
			<span>x</span>
		<?php endif; ?>

		<input type="text" data-bind="value: price" class="form-input--price" name="<?php echo esc_attr( $input_prefix ); ?>[price]" value="<?php echo esc_attr( $service_data['price'] ); ?>">
		<span class="abrs-badge" data-bind="html: total"></span>
		<a href="#" class="tippy" style="color: #999;" title="<?php esc_html_e( 'Refresh', 'awebooking' ); ?>"><i class="dashicons dashicons-update"></i></a>
	</div>
</li>
