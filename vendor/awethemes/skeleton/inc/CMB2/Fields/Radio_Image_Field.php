<?php
namespace Skeleton\CMB2\Fields;

class Radio_Image_Field extends Field_Abstract {
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
		add_filter( 'cmb2_list_input_attributes', array( $this, 'set_image_label' ), 10, 4 );
		print $field_type_object->radio(); // WPCS: XSS OK.
	}

	/**
	 * Overwrite image label only for `radio_image` field type.
	 *
	 * @param array  $args              The array of attribute arguments.
	 * @param array  $type_defaults          The array of default values.
	 * @param array  $field             The `CMB2_Field` object.
	 * @param object $field_type_object This `CMB2_Types` object.
	 */
	public function set_image_label( $args, $type_defaults, $field, $field_type_object ) {
		if ( 'radio_image' === $field->args['type'] ) {
			list( $image_src, $label ) = $this->get_image_label( $args['label'] );
			$args['label'] = '<img src="' . esc_attr( $image_src ) . '" alt="' . esc_attr( $label ) . '">';
		}

		return $args;
	}

	/**
	 * Return a array with image, label from raw label.
	 *
	 * @param  mixed $input Raw label input.
	 * @return array
	 */
	protected function get_image_label( $input ) {
		$input = (array) $input;

		if ( isset( $input['src'] ) ) {
			$image_src = $input['src'];
			$label = isset( $input['label'] ) ? $input['label'] : '';
		} else {
			$image_src = $input[0];
			$label = isset( $input[1] ) ? $input[1] : '';
		}

		return array( $image_src, $label );
	}
}
