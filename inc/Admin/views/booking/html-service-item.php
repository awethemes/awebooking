<?php
$input_prefix = 'services[' . $service_selection->get_id() . ']';
$service = $service_selection['service'];
?>

<li class="abrs-sortable__item">
	<div class="abrs-sortable__head">
		<span class="abrs-sortable__order">
			<input type="hidden" name="<?php echo esc_attr( $input_prefix ); ?>[id]" value="<?php echo esc_attr( $service->get_id() ); ?>">

			<?php if ( $service->is_quantity_selectable() ) : ?>
				<?php $quantity = $services_exist->where( 'service_id', '=', $service->get_id() )->pluck( 'quantity' )->first(); ?>

				<input type="number" min="0" class="form-input" value="<?php echo $quantity ? absint( $quantity ) : 0; ?>" name="<?php echo esc_attr( $input_prefix ); ?>[quantity]">

			<?php else : ?>

				<div class="nice-checkbox">
					<input type="checkbox" id="service_id_<?php echo esc_attr( $service->get_id() ); ?>" name="<?php echo esc_attr( $input_prefix ); ?>[quantity]" value="1" <?php checked( in_array( $service->get_id(), $service_ids ) ); ?> />
				</div>

			<?php endif; ?>
		</span>
	</div>

	<div class="abrs-sortable__body">
		<a href="<?php echo esc_url( get_edit_post_link( $service->get_id() ) ); ?>" title="<?php echo esc_attr( $service->get( 'name' ) ); ?>" target="_blank">
			<strong><?php echo esc_html( $service->get( 'name' ) ); ?></strong>
		</a>
	</div>

	<div class="abrs-sortable__actions">

			<span class="abrs-badge">123</span>
	</div>

</li>