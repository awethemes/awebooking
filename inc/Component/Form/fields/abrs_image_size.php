<?php
/**
 * Print the field content.
 *
 * @package AweBooking
 *
 * @var $field, $escaped_value, $object_id, $object_type, $types
 */

$escaped_value = wp_parse_args( $escaped_value, [
	'width'  => 150,
	'height' => 150,
	'crop'   => 'on',
]);

?><div class="abrs-input-addon dp-inline-flex">
	<?php
	print $types->input([ // WPCS: XSS OK.
		'type'         => 'text',
		'id'           => $types->_id( '_width' ),
		'name'         => $types->_name( '[width]' ),
		'value'        => $escaped_value['width'],
		'style'        => 'width: 65px',
	]);

	echo '<label>x</label>';

	print $types->input([ // WPCS: XSS OK.
		'type'         => 'text',
		'id'           => $types->_id( '_height' ),
		'name'         => $types->_name( '[height]' ),
		'value'        => $escaped_value['height'],
		'style'        => 'width: 65px',
	]);
	?>
</div>

<?php
print $types->checkbox([ // WPCS: XSS OK.
	'id'           => $types->_id( '_crop' ),
	'name'         => $types->_name( '[crop]' ),
	'value'        => $escaped_value['crop'],
], 'on' === $escaped_value['crop'] );
?>

<label for="<?php echo esc_attr( $types->_id( '_crop' ) ); ?>"><?php esc_html_e( 'Hard crop?', 'awebooking' ); ?></label>
