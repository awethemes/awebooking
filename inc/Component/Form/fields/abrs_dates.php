<?php
/**
 * Print the field content.
 *
 * @package AweBooking
 *
 * @var $field, $escaped_value, $object_id, $object_type, $types
 */

$escaped_value = is_array( $escaped_value ) ? $escaped_value : [];

wp_enqueue_script( 'flatpickr' );
wp_enqueue_script( 'flatpickr-range-plugin' );

?><div class="abrs-input-dates abrow no-gutters">
	<div class="abcol-6">
		<i class="afc afc-calendar"></i>
		<?php
		print $types->input([ // WPCS: XSS OK.
			'type'        => 'text',
			'id'          => $types->_id( '_start' ),
			'name'        => 'check-in',
			'value'       => isset( $escaped_value[0] ) ? $escaped_value[0] : '',
			'placeholder' => esc_html__( 'Check In', 'awebooking' ),
		]);
		?>
	</div>

	<div class="abcol-6">
		<?php
		print $types->input([ // WPCS: XSS OK.
			'type'        => 'text',
			'id'          => $types->_id( '_end' ),
			'name'        => 'check-out',
			'value'       => isset( $escaped_value[1] ) ? $escaped_value[1] : '',
			'placeholder' => esc_html__( 'Check Out', 'awebooking' ),
		]);
		?>
	</div>
</div>

<?php if ( false !== $field->prop( 'show_js' ) ) : ?>
<script type="text/javascript">
(function($) {
	'use strict';

	$(function() {
		flatpickr('#<?php echo esc_attr( $types->_id( '_start' ) ); ?>', {
			mode: 'range',
			altInput: true,
			altFormat: 'F j d',
			dateFormat: 'Y-m-d',
			showMonths: 2,
			plugins: [ new rangePlugin({ input: '#<?php echo esc_attr( $types->_id( '_end' ) ); ?>' }) ],
			onValueUpdate: function(dates, formatted, instance) {
				instance.input.value = instance.formatDate(dates[0], instance.config.dateFormat);
			},
		});
	});

})(jQuery);
</script>
<?php endif ?>
