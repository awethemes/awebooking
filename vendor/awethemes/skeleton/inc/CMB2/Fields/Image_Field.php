<?php
namespace Skeleton\CMB2\Fields;

class Image_Field extends Field_Abstract {
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
		// Display the preview image.
		$value = $escaped_value;

		if ( is_numeric( $value ) ) {
			$value = wp_get_attachment_image_url( $value, array( 150, 150 ) );
		}

		$preview_image = '';
		if ( ! empty( $value ) ) {
			$preview_image = "<i class='dashicons dashicons-no-alt remove'></i><img src='" . esc_url( $value ) . "'>";
		}

		echo "<div class='cmb2-image-upload'><div class='thumbnail tf-image-preview'>" . $preview_image . '</div></div>';

		echo $field_type_object->hidden( array( // WPCS: XSS OK.
			'name'  => $field_type_object->_name(),
			'id'    => $field_type_object->_id(),
			'value' => $escaped_value,
		) );
	}
}
