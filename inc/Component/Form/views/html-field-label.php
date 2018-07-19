<?php
/**
 * Display the field label.
 *
 * @package AweBooking\Component\Form
 */

/* @var \CMB2_Field $field */

$required = '';
if ( $field->prop( 'required' ) || $field->args( 'attribues', 'required' ) ) {
	$required = '<span class="required">*</span>';
}

$tooltip = '';
if ( $field->prop( 'tooltip' ) ) {
	$title = $field->prop( 'tooltip' );

	if ( true === $title ) {
		$title = $field->prop( 'description' );
		$field->set_prop( 'tooltip', $title );
		$field->set_prop( 'description', '' );
	}

	$tooltip = '<span class="cmb2-tooltip tippy" title="' . esc_attr( $title ) . '"><i class="dashicons dashicons-editor-help"></i></span>';
}

$translation_html = '';
if ( $field->prop( 'translatable' ) && abrs_running_on_multilanguage() ) {
	$translation_html = '<span class="dashicons dashicons-translation"></span>';
}

$style = ! $field->args( 'show_names' ) ? ' style="display:none;"' : '';
return sprintf( "\n" . '<label%1$s for="%2$s">%6$s %3$s %4$s</label> %5$s' . "\n", $style, $field->id(), $field->args( 'name' ), $required, $tooltip, $translation_html );
