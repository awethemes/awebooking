<?php
/* @vars $booked */

$input_prefix = 'services[' . $booked->get( 'service_id' ) . ']';
?>

<li class="abrs-sortable__item">
	<div class="abrs-sortable__head">
		<span class="abrs-sortable__order">
			<input type="hidden" name="<?php echo esc_attr( $input_prefix ); ?>[id]" value="<?php echo esc_attr( $booked->get( 'service_id' ) ); ?>">

			<input type="number" data-bind="value: quantity" min="0" class="form-input form-input--quantity" value="<?php echo absint( $booked->get( 'quantity' ) ); ?>" name="<?php echo esc_attr( $input_prefix ); ?>[quantity]">
		</span>
	</div>

	<div class="abrs-sortable__body">
		<?php if ( $booked->get( 'name' ) ) : ?>
			<span>
				<strong><?php echo esc_html( $booked->get( 'name' ) ); ?></strong>
			</span>
		<?php endif; ?>

		<span class="sup-placeholder">
			<?php esc_html_e( 'deleted', 'awebooking' ); ?>
		</span>

	</div>

	<div class="abrs-sortable__actions">
		<input type="text" data-bind="value: price" class="form-input--price" name="<?php echo esc_attr( $input_prefix ); ?>[price]" value="<?php echo esc_attr( $booked->get( 'price' ) ); ?>">
		<span class="abrs-badge" data-bind="html: total"></span>
		<a href="#" class="tippy" style="color: #999;" title="<?php esc_html_e( 'Refresh', 'awebooking' ); ?>"><i class="dashicons dashicons-update"></i></a>
	</div>
</li>
