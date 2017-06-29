<?php
namespace Skeleton\CMB2\Fields;

class Icon_Field extends Field_Abstract {
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
		$escaped_value = wp_parse_args( $escaped_value, array(
			'type' => '',
			'icon' => '',
		));

		printf( '<div id="%s" class="ipf">', $field_type_object->_id() );
		printf( '<a class="ipf-select">%s</a>', esc_html__( 'Select Icon', 'skeleton' ) );
		printf( '<a class="ipf-remove button hidden">%s</a>', esc_html__( 'Remove', 'skeleton' ) );

		print $field_type_object->input( array( // WPCS: XSS OK.
			'type'    => 'hidden',
			'id'      => $field_type_object->_id( '[type]' ),
			'name'    => $field_type_object->_name( '[type]' ),
			'class'   => 'ipf-type',
			'value'   => $escaped_value['type'],
		) );

		print $field_type_object->input( array( // WPCS: XSS OK.
			'type'    => 'hidden',
			'id'      => $field_type_object->_id( '[icon]' ),
			'name'    => $field_type_object->_name( '[icon]' ),
			'class'   => 'ipf-icon',
			'value'   => $escaped_value['icon'],
		) );

		// This won't be saved. It's here for the preview.
		printf( '<input type="hidden" class="url" value="%s">',
			esc_attr( $this->get_icon_url( $escaped_value['type'], $escaped_value['icon'] ) )
		);

		echo '</div>';

		wp_enqueue_media();
		$field->add_js_dependencies( array( 'icon-picker' ) );
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
		return wp_parse_args( $value, array(
			'type' => '',
			'icon' => '',
		));
	}

	/**
	 * Get Icon URL
	 *
	 * @param  string  $type  Icon type.
	 * @param  mixed   $id    Icon ID.
	 * @param  string  $size  Optional. Icon size, defaults to 'thumbnail'.
	 *
	 * @return string
	 */
	protected function get_icon_url( $type, $id, $size = 'thumbnail' ) {
		$url = '';

		if ( ! in_array( $type, array( 'image', 'svg' ) ) ) {
			return $url;
		}

		if ( empty( $id ) ) {
			return $url;
		}

		return wp_get_attachment_image_url( $id, $size, false );
	}
}
