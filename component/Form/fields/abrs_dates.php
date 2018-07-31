<?php
/**
 * Print the field content.
 *
 * @package AweBooking
 *
 * @var $field, $escaped_value, $object_id, $object_type, $types
 */

$escaped_value = is_array( $escaped_value ) ? $escaped_value : [];

$input_names = is_array( $field->args( 'input_names' ) ) ? $field->args( 'input_names' ) : [];

wp_enqueue_script( 'flatpickr' );

?><div class="abrs-input-dates">
	<?php
	print $types->input([ // WPCS: XSS OK.
		'type'         => 'text',
		'id'           => $types->_id( '_start' ),
		'name'         => isset( $input_names[0] ) ? $input_names[0] : 'check-in',
		'value'        => isset( $escaped_value[0] ) ? $escaped_value[0] : '',
		'placeholder'  => esc_html__( 'Check In', 'awebooking' ),
		'autocomplete' => 'off',
	]);

	print $types->input([ // WPCS: XSS OK.
		'type'         => 'text',
		'id'           => $types->_id( '_end' ),
		'name'         => isset( $input_names[1] ) ? $input_names[1] : 'check-out',
		'value'        => isset( $escaped_value[1] ) ? $escaped_value[1] : '',
		'placeholder'  => esc_html__( 'Check Out', 'awebooking' ),
		'autocomplete' => 'off',
	]);
	?>
</div>

<?php if ( false !== $field->prop( 'show_js' ) ) : ?>
<script type="text/javascript">
(function($) {
	'use strict';

	$(function() {
		flatpickr('#<?php echo esc_attr( $types->_id( '_start' ) ); ?>', {
			mode: 'range',
			minDate: 'today',
			dateFormat: 'Y-m-d',
			showMonths: awebooking.isMobile() ? 1 : 2,
			plugins: [ new awebooking.utils.flatpickrRangePlugin({ input: '#<?php echo esc_attr( $types->_id( '_end' ) ); ?>' }) ],
		});
	});

})(jQuery);
</script>
<?php endif ?>
