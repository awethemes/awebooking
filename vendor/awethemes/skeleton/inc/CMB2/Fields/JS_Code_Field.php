<?php
namespace Skeleton\CMB2\Fields;

class JS_Code_Field extends HTML_Code_Field {
	/**
	 * Text editor mode.
	 *
	 * @var string
	 */
	protected $mode = 'javascript';

	/**
	 * Escape sanitization callback.
	 *
	 * @var string
	 */
	protected $escape_callback = null;

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
		return trim( preg_replace( '#<script[^>]*>(.*)</script>#is', '$1', $value ) );
	}
}
