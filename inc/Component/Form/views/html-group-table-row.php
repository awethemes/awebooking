<?php

$field_group->peform_param_callback( 'before_group_row' );
$closed_class = $field_group->options( 'closed' ) ? ' closed' : '';

echo '<div class="cmb-repeatable-grouping', $closed_class, '" data-iterator="', $field_group->index, '">';

if ( $field_group->args( 'repeatable' ) ) {
	// echo '<button type="button" data-selector="', $field_group->id(), '_repeat" class="dashicons-before dashicons-no-alt cmb-remove-group-row" title="', esc_attr( $field_group->options( 'remove_button' ) ), '"></button>';
}

// echo '<div class="cmbhandle" title="' , esc_attr__( 'Click to toggle', 'cmb2' ), '"><br></div><h3 class="cmb-group-title cmbhandle-title"><span>', $field_group->replace_hash( $field_group->options( 'group_title' ) ), '</span></h3>';

// echo '<div class="inside cmb-td cmb-nested cmb-field-list">';

// Loop and render repeatable group fields.
foreach ( array_values( $field_group->args( 'fields' ) ) as $field_args ) {
	if ( 'hidden' === $field_args['type'] ) {

		// Save rendering for after the metabox.
		$this->add_hidden_field( $field_args, $field_group );

	} else {

		$field_args['show_names'] = $field_group->args( 'show_names' );
		$field_args['context']    = $field_group->args( 'context' );

		$field = $this->get_field( $field_args, $field_group )->render_field();
	}
}

if ( $field_group->args( 'repeatable' ) ) {
	/*echo '<div class="cmb-row cmb-remove-field-row">
		<div class="cmb-remove-row">
			<button type="button" data-selector="', $field_group->id(), '_repeat" class="cmb-remove-group-row cmb-remove-group-row-button alignright button-secondary">', $field_group->options( 'remove_button' ), '</button>
		</div>
	</div>';*/
}

echo '</div><!-- /.cmb-repeatable-grouping -->';

$field_group->peform_param_callback( 'after_group_row' );
