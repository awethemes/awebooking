<?php
namespace Skeleton\CMB2\Fields;

class HTML_Code_Field extends Field_Abstract {
	/**
	 * Text editor mode.
	 *
	 * @var string
	 */
	protected $mode = 'html';

	/**
	 * Escape sanitization callback.
	 *
	 * @var string
	 */
	protected $escape_callback = 'wp_kses_post';

	/**
	 * Render custom field type callback.
	 *
	 * @param \CMB2_Field $field              The passed in `CMB2_Field` object.
	 * @param mixed       $escaped_value      The value of this field escaped.
	 * @param int|string  $object_id          The ID of the current object.
	 * @param string      $object_type        The type of object you are working with.
	 * @param \CMB2_Types $field_type_object  The `CMB2_Types` object.
	 */
	public function output( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
		printf( '<pre id="%s-code-editor" class="cmb2-code-editor"></pre>', esc_attr( $field_type_object->_id() ) );

		print $field_type_object->textarea( array( // WPCS: XSS OK.
			'style'     => 'display: none',
			'class'     => 'cmb2-textarea-code',
			'value'     => $this->escape_callback ? $field->escaped_value( $this->escape_callback ) : $field->value(),
			'data-mode' => $this->mode,
		));

		$field->add_js_dependencies( array( 'ace-editor', 'ace-ext-language_tools' ) );
	}

	/**
	 * Filter the value before it is saved.
	 *
	 * @param bool|mixed     $override_value Sanitization/Validation override value to return.
	 * @param mixed          $value      The value to be saved to this field.
	 * @param int            $object_id  The ID of the object where the value will be saved.
	 * @param array          $field_args The current field's arguments.
	 * @param \CMB2_Sanitize $sanitizer  The `CMB2_Sanitize` object.
	 */
	public function sanitization( $override_value, $value, $object_id, $field_args, $sanitizer ) {
		return wp_kses_post( $value );
	}
}
