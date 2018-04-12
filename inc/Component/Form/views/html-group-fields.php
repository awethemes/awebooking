<?php

$field_group->index = 0;
$group_val = (array) $field_group->value();

$field_group->peform_param_callback( 'before_group' );
echo '<div class="cmb-row ' . esc_attr( $field_group->row_classes() ) . '" data-fieldtype="group">';

// Print the name.
if ( ! $field_group->args( 'show_names' ) ) {
	echo "\n\t<div class=\"cmb-td\">\n";

	$field_group->peform_param_callback( 'label_cb' );
} else {
	if ( $_label = $field_group->get_param_callback_result( 'label_cb' ) ) {
		echo '<div class="cmb-th">' . $_label . '</div>'; // WPCS: XSS OK.
	}

	echo "\n\t<div class=\"cmb-td\">\n";
}

// Print the description.
if ( $_desc = $field_group->args( 'description' ) ) {
	echo '<p class="cmb2-metabox-description">' . $_desc . '</p>'; // WPCS: XSS OK.
}

echo '<div data-groupid="' . esc_attr( $field_group->id() ) . '" id="' . esc_attr( $field_group->id() ) . '_repeat" ' . $this->group_wrap_attributes( $field_group ) . '>'; // WPCS: XSS OK.
echo '<div class="cmb-repeat-group-wrap cmb2-flex-table">';

// Show the heading columns.
echo '<div class="cmb2-flex-tr">';
foreach ( $field_group->args( 'fields' ) as $field_args ) {
	echo '<div class="cmb2-flex-th">' . ( isset( $field_args['name'] ) ? $field_args['name'] : '' ) . '</div>'; // WPCS: XSS OK.
}
echo '</div>';

if ( empty( $group_val ) ) {

	include trailingslashit( __DIR__ ) . 'html-group-table-row.php';

} else {
	foreach ( $group_val as $group_key => $field_id ) {
		include trailingslashit( __DIR__ ) . 'html-group-table-row.php';
		$field_group->index++;
	}
}

if ( $field_group->args( 'repeatable' ) ) {
	echo '<p class="cmb-add-row"><button type="button" data-selector="' . esc_attr( $field_group->id() ) . '_repeat" data-grouptitle="' . esc_attr( $field_group->options( 'group_title' ) ) . '" class="cmb-add-group-row button-secondary">' . $field_group->options( 'add_button' ) . '</button></p>'; // WPCS: XSS OK.
}

echo '</div></div><!-- /.cmb-repeatable-group -->';
echo '</div></div><!-- /.cmb-row -->';

$field_group->peform_param_callback( 'after_group' );
