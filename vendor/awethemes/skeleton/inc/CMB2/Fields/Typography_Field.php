<?php
namespace Skeleton\CMB2\Fields;

class Typography_Field extends Field_Abstract {
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
		$options = '';

		foreach ( $this->container['webfonts']->get_all_fonts( 'assoc' ) as $fonts ) {
			$options .= '<optgroup label="' . $fonts['label'] . '">';

			foreach ( $fonts['fonts'] as $font_name => $font_info ) {
				$options .= $field_type_object->select_option( array(
					'value'   => $font_name,
					'label'   => $font_info['display_name'],
					'checked' => $font_name === $escaped_value,
				));
			}

			$options .= '</optgroup>';
		}

		echo $field_type_object->select( array( // WPCS: XSS OK.
			'options' => $options,
		) );
	}
}
