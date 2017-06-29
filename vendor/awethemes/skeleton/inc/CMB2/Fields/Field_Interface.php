<?php
namespace Skeleton\CMB2\Fields;

interface Field_Interface {
	/**
	 * Render custom field type callback.
	 *
	 * @param \CMB2_Field $field              The passed in `CMB2_Field` object.
	 * @param mixed       $escaped_value      The value of this field escaped.
	 * @param int|string  $object_id          The ID of the current object.
	 * @param string      $object_type        The type of object you are working with.
	 * @param \CMB2_Types $field_type_object  The `CMB2_Types` object.
	 */
	public function output( $field, $escaped_value, $object_id, $object_type, $field_type_object );

	/**
	 * Filter the value before it is saved.
	 *
	 * @param bool|mixed     $override_value Sanitization/Validation override value to return.
	 * @param mixed          $value      The value to be saved to this field.
	 * @param int            $object_id  The ID of the object where the value will be saved.
	 * @param array          $field_args The current field's arguments.
	 * @param \CMB2_Sanitize $sanitizer  The `CMB2_Sanitize` object.
	 */
	public function sanitization( $override_value, $value, $object_id, $field_args, $sanitizer );

	/**
	 * Filter field types that are non-repeatable.
	 *
	 * @param  array $fields Array of fields designated as non-repeatable.
	 * @return array
	 */
	public function disable_repeatable( $fields );
}
