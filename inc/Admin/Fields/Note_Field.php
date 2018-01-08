<?php
namespace AweBooking\Admin\Fields;

use Skeleton\Fields\CMB2_Field;

class Note_Field extends CMB2_Field {
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
		echo '<div class="awebooking-sweet-alert">';

		if ( $alert_title = $field->prop( 'title' ) ) {
			echo '<h4>', esc_html( $alert_title ), '</h4>';
		}

		echo wp_kses_post( wpautop( $field->desc() ) );
		echo '</div>';
	}
}
