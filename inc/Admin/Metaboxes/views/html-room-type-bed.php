<?php

$value = wp_parse_args( $field->escaped_value(), [
	'type'   => '',
	'number' => 1,
]);
?>
<div class="cmb2-flex-table">
	<div class="cmb-repeatable-grouping">
		<div class="cmb-row">
			<?php
				echo $types->input([
					'id'     => $types->_id( '_type' ),
					'name'   => $types->_name( '[type]' ),
					'value'  => $value['type'],
					'list'   => 'bed_type_list',
				]);

				$type_list = apply_filters( 'awebooking/bed_type_list', [
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

				echo '<datalist id="bed_type_list">';
				foreach ( $type_list as $type ) {
					echo '<option value="' . $type . '">'; // WPCS: xss ok.
				}
				echo '</datalist>';
			?>
		</div>

		<?php
			$number_list = array_combine( $r = range( 1, 12 ), $r );

			$number_options = '';
			foreach ( $number_list as $key => $number ) {
				$number_options .= '<option value="'. $key .'" '. selected( $value['number'], $key, false ) .'>'. $number .'</option>';
			}
		?>
		<div class="cmb-row">
			<?php
			echo $types->select([
				'id'     => $types->_id( '_number' ),
				'name'   => $types->_name( '[number]' ),
				'value'  => $value['number'],
				'options' => $number_options,
			]);
			?>
		</div>
	</div>
</div>
