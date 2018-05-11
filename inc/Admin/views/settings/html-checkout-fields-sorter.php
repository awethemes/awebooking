<?php

use AweBooking\Frontend\Checkout\Form_Controls;

$controls = new Form_Controls;

$mandatory = $controls->get_mandatory();

$enabled_controls = abrs_get_option( 'list_checkout_fields' );

?>

<ul class="abrs-sortable" id="js-sorting-checkout-fields">
	<?php foreach ( $controls->prop( 'fields' ) as $field ) : ?>
		<li class="abrs-sortable__item">
			<div class="abrs-sortable__head">
				<span class="abrs-sortable__handle"></span>

				<span class="abrs-sortable__order">
					<input type="checkbox" name="list_checkout_fields[]" value="<?php echo esc_attr( $field['id'] ); ?>" <?php echo in_array( $field['id'], $mandatory ) ? 'disabled="disabled"' : ''; ?> <?php checked( in_array( $field['id'], $enabled_controls ) ); ?>>
				</span>
			</div>

			<div class="abrs-sortable__body">
				<strong><?php echo esc_html( isset( $field['name'] ) ? $field['name'] : $field['id'] ); ?></strong>
				<span class="sup-placeholder"><?php echo esc_html( $field['type'] ); ?></span>
			</div>

			<div class="abrs-sortable__actions">
				<?php if ( isset( $field['required'] ) && $field['required'] ) : ?>
					<span class="abrs-badge"><?php esc_html_e( 'Required', 'awebooking' ); ?></span>
				<?php endif ?>
			</div>
		</li>
	<?php endforeach ?>
</ul><!-- /.abrs-sortable -->

<script type="text/javascript">
(function($) {
	'use strict';

	$(function() {
		Sortable.create($('#js-sorting-checkout-fields')[0], {
			handle: '.abrs-sortable__handle',
			animation: 150,
		});
	});

})(jQuery);
</script>
