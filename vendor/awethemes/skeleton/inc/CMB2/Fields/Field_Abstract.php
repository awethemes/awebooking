<?php
namespace Skeleton\CMB2\Fields;

use Skeleton\Support\Utils;
use Skeleton\Container\Container;
use Skeleton\Support\Multidimensional;

abstract class Field_Abstract implements Field_Interface {
	/**
	 * Field type.
	 *
	 * @var string
	 */
	public $type;

	/**
	 * Adding this field to the blacklist of repeatable field-types.
	 *
	 * @var boolean
	 */
	public $repeatable = true;

	/**
	 * The passed in `CMB2_Field` object.
	 *
	 * @var \CMB2_Field
	 */
	protected $field;

	/**
	 * The `CMB2_Types` object.
	 *
	 * @var \CMB2_Types
	 */
	protected $field_type_object;

	/**
	 * Skeleton container instance.
	 *
	 * @var Container
	 */
	protected $container;

	/**
	 * Create a CMB2 custom field type.
	 *
	 * @param Container $container Skeleton container instance.
	 * @param string    $type      Custom field type.
	 */
	public function __construct( Container $container, $type ) {
		$this->type = $type;
		$this->container = $container;
	}

	/**
	 * This method will run after hook register field.
	 */
	public function hooks() {}

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
		/**
		 * Save this `field` so another method can be use late.
		 *
		 * @var \CMB2_Field
		 */
		$this->field = $field;

		/**
		 * Save this `field_type_object` similar `field`.
		 *
		 * @var \CMB2_Types
		 */
		$this->field_type_object = $field_type_object;
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
	public function sanitization( $override_value, $value, $object_id, $field_args, $sanitizer ) {}

	/**
	 * Filter field types that are non-repeatable.
	 *
	 * @param  array $fields Array of fields designated as non-repeatable.
	 * @return array
	 */
	public function disable_repeatable( $fields ) {
		$fields[ $this->type ] = 1;
		return $fields;
	}

	/**
	 * A tiny helper to clone field.
	 *
	 * @param  string $clone_suffix Clone field suffix name.
	 * @param  array  $args         Optional, custom clone field arguments.
	 * @return CMB2_Field
	 */
	protected function clone_field( $clone_suffix, $args = array() ) {
		$base_id = $this->field->group ? $this->field->args( '_id' ) : $this->field_type_object->_id();

		return $this->field->get_field_clone( wp_parse_args( $args, array(
			'id'      => Utils::clone_id( $base_id, $clone_suffix ),
			'default' => $this->field->args( 'default_' . $clone_suffix ),
		) ) );
	}
}
