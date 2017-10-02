<?php
namespace AweBooking\Admin\Fields;

use AweBooking\Factory;
use Skeleton\Fields\CMB2_Field;

class Service_List_Field extends CMB2_Field {
	/**
	 * Adding this field to the blacklist of repeatable field-types.
	 *
	 * @var boolean
	 */
	public $repeatable = false;

	/**
	 * Render custom field type callback.
	 *
	 * @param CMB2_Field $field             The passed in `CMB2_Field` object.
	 * @param mixed      $escaped_value     The value of this field escaped.
	 * @param string|int $object_id         The ID of the current object.
	 * @param string     $object_type       The type of object you are working with.
	 * @param CMB2_Types $field_type_object The `CMB2_Types` object.
	 */
	public function output( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
		$escaped_value = $escaped_value ? wp_parse_id_list( $escaped_value ) : [];
		$room_type = Factory::get_room_type( $field->args( 'room_type' ) );

		if ( ! $room_type->exists() ) {
			printf( esc_html__( 'No service available for this room', 'awebooking' ) );
			return;
		}

		$services = $room_type->get_services(); ?>

		<ul class="cmb2-checkbox-list cmb2-list">
		<?php foreach ( $services as $service ) : ?>
			<li>
				<label>
					<?php $checked = in_array( $service->get_id(), $escaped_value ) ? 'checked=""' : ''; ?>
					<input type="checkbox" class="cmb2-option" name="<?php echo esc_attr( $field_type_object->_name() ); ?>[]" value="<?php echo esc_attr( $service->get_id() ); ?>" <?php echo $checked; ?>>
					<span><strong><?php echo esc_html( $service->get_name() ); ?></strong><i><?php echo $service->get_describe(); ?></i></span>
				</label>
			</li>
		<?php endforeach; ?>
		</ul>

		<?php
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
		return $value; // TODO: ...
	}
}
