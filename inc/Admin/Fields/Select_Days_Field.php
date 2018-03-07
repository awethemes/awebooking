<?php
namespace AweBooking\Admin\Fields;

use AweBooking\Dropdown;
use Skeleton\Fields\CMB2_Field;

class Select_Days_Field extends CMB2_Field {
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
		$field->set_prop( 'options_cb', Dropdown::cb( 'get_week_days' ) );

		$field->set_prop( 'select_all_button', false );

		print $field_type_object->multicheck_inline(); // @codingStandardsIgnoreLine
	}
}
