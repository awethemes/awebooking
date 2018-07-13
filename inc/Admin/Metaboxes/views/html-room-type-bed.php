<?php

$value = wp_parse_args( $field->escaped_value(), [
	'type'   => '',
	'number' => 1,
]);

$type_list = apply_filters( 'abrs_list_beds', [
	esc_html__( 'Single bed', 'awebooking' ),
	esc_html__( 'Double bed', 'awebooking' ),
	esc_html__( 'Queen bed', 'awebooking' ),
	esc_html__( 'King bed', 'awebooking' ),
	esc_html__( 'Twin bed', 'awebooking' ),
	esc_html__( 'Super King bed', 'awebooking' ),
	esc_html__( 'Futon bed', 'awebooking' ),
	esc_html__( 'Murphy bed', 'awebooking' ),
	esc_html__( 'Sofa bed', 'awebooking' ),
	esc_html__( 'Tatami Mats bed', 'awebooking' ),
	esc_html__( 'Run of the House', 'awebooking' ),
	esc_html__( 'Dorm bed', 'awebooking' ),
	esc_html__( 'Roll-Away bed', 'awebooking' ),
	esc_html__( 'Crib', 'awebooking' ),
	esc_html__( 'Unspecified bed', 'awebooking' ),
]);

?>

<div class="dp-flex">
	<?php
	echo $types->input([ // WPCS: XSS OK.
		'id'     => $types->_id( '_type' ),
		'name'   => $types->_name( '[type]' ),
		'value'  => $value['type'],
		'list'   => 'bed_type_list',
	]);

	$number_list = array_combine( $r = range( 1, 12 ), $r );
	$number_options = '';
	foreach ( $number_list as $key => $number ) {
		$number_options .= '<option value="' . $key . '" ' . selected( $value['number'], $key, false ) . '>' . $number . '</option>';
	}

	echo $types->select([ // WPCS: XSS OK.
		'id'      => $types->_id( '_number' ),
		'name'    => $types->_name( '[number]' ),
		'value'   => $value['number'],
		'options' => $number_options,
		'style'   => 'width: 80px;',
	]);
	?>
</div>

<datalist id="bed_type_list">
<?php
foreach ( $type_list as $type ) {
	echo '<option value="' . $type . '">'; // WPCS: xss ok.
}
?>
</datalist>
