<?php

use AweBooking\Checkout\Form_Controls;

$controls = new Form_Controls;
$controls->prepare_fields();

$enable_ids = $controls->get_enable_controls();
$mandatory_ids = $controls->get_mandatory_controls();

?>

<ul class="abrs-sortable-group">

	<?php foreach ( $controls->sections() as $section ) : ?>
		<li class="abrs-sortable-group__item">
			<span class="abrs-sortable-group__label"><?php echo esc_html( $section->title ); ?></span>

			<ul class="abrs-sortable js-sorting-checkout-fields">
				<?php foreach ( $section->fields as $field ) : ?>
					<li class="abrs-sortable__item">
						<div class="abrs-sortable__head">
							<span class="abrs-sortable__order">
								<input type="checkbox" name="list_checkout_controls[]" value="<?php echo esc_attr( $field['id'] ); ?>" <?php echo in_array( $field['id'], $mandatory_ids ) ? 'disabled="disabled"' : ''; ?> <?php checked( in_array( $field['id'], $enable_ids ) ); ?>>
							</span>
						</div>

						<div class="abrs-sortable__body">
							<span><?php echo esc_html( isset( $field['name'] ) ? $field['name'] : $field['id'] ); ?></span>
							<span class="sup-placeholder"><?php echo esc_html( $field['type'] ); ?></span>
						</div>

						<div class="abrs-sortable__actions">
							<?php if ( isset( $field['required'] ) && $field['required'] ) : ?>
								<span class="abrs-badge"><?php esc_html_e( 'required', 'awebooking' ); ?></span>
							<?php endif ?>
						</div>
					</li>

				<?php endforeach ?>
			</ul><!-- /.abrs-sortable -->

		</li>
	<?php endforeach ?>
</ul>
