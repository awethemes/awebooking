<?php
namespace Skeleton\CMB2\Fields;

use Skeleton\Support\Utils;
use Skeleton\Support\Multidimensional;

class Link_Color_Field extends Field_Abstract {
	/**
	 * Adding this field to the blacklist of repeatable field-types.
	 *
	 * @var boolean
	 */
	public $repeatable = false;

	/**
	 * Render custom field type callback.
	 *
	 * NOTE: This field unable use in a `group` field.
	 *
	 * @param CMB2_Field $field              The passed in `CMB2_Field` object.
	 * @param mixed      $escaped_value      The value of this field escaped.
	 * @param int|string $object_id          The ID of the current object.
	 * @param string     $object_type        The type of object you are working with.
	 * @param CMB2_Types $field_type_object  The `CMB2_Types` object.
	 */
	public function output( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
		parent::output( $field, $escaped_value, $object_id, $object_type, $field_type_object );

		// Enqueue `wp-color-picker-alpha` script.
		wp_enqueue_style( 'wp-color-picker' );
		$field->add_js_dependencies( array( 'wp-color-picker-alpha' ) );

		?><table class="link-color-table">
			<thead>
				<tr>
					<th><label><?php echo esc_html( $field_type_object->_text( 'normal_color', esc_html__( 'Color', 'skeleton' ) ) ); ?></th>
					<th><label><?php echo esc_html( $field_type_object->_text( 'hover_color',  esc_html__( 'Hover color', 'skeleton' ) ) ); ?></th>
					<th><label><?php echo esc_html( $field_type_object->_text( 'active_color', esc_html__( 'Active color', 'skeleton' ) ) ); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="3"><?php print $field_type_object->_desc(); // WPCS: XSS OK. ?></td>
				</tr>
			</tfoot>
			<tbody>
				<tr>
					<td>
						<?php print $field_type_object->input( $this->colorpicker_args() ); // WPCS: XSS OK. ?>
					</td>

					<td>
						<?php
						// Clone `field_type_object` and re-setting field object with new clone field.
						$cmb2_types = clone $field_type_object;
						$cmb2_types->field = $this->clone_field( 'hover' );

						print $cmb2_types->input( $this->colorpicker_args( 'default_hover' ) ); // WPCS: XSS OK. ?>
					</td>

					<td>
						<?php
						// Clone `field_type_object` and re-setting field object with new clone field.
						$cmb2_types = clone $field_type_object;
						$cmb2_types->field = $this->clone_field( 'active' );

						print $cmb2_types->input( $this->colorpicker_args( 'default_active' ) ); // WPCS: XSS OK. ?>
					</td>
				</tr>
			</tbody>
		</table><?php
	}

	/**
	 * Filter the value before it is saved.
	 *
	 * @param bool|mixed    $override_value Sanitization/Validation override value to return.
	 * @param mixed         $value      The value to be saved to this field.
	 * @param int           $object_id  The ID of the object where the value will be saved.
	 * @param array         $field_args The current field's arguments.
	 * @param CMB2_Sanitize $sanitizer  The `CMB2_Sanitize` object.
	 */
	public function sanitization( $override_value, $value, $object_id, $field_args, $sanitizer ) {
		$data_to_save = $sanitizer->field->data_to_save;
		$base_id = $sanitizer->field->id();

		// Save extends fields first.
		foreach ( array( 'hover', 'active' ) as $key ) {
			$field_id = Utils::clone_id( $base_id, $key );
			$field = $sanitizer->_new_supporting_field( $field_id );

			// Get data in data_to_save with multidimensional support.
			$save_value = Multidimensional::find( $data_to_save, $field_id );

			// Save this extra field.
			$field->save_field( $save_value );
		}

		return $sanitizer->colorpicker();
	}

	/**
	 * Default colorpicker args.
	 *
	 * @param  string $default Default key ID.
	 * @return array
	 */
	protected function colorpicker_args( $default = 'default' ) {
		return array(
			'desc'  => '', // No description.
			'class' => 'cmb2-colorpicker cmb2-text-small',
			'data-alpha' => true,
			'data-default-color' => $this->field->args( $default ),
		);
	}
}
