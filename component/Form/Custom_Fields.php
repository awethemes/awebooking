<?php
namespace AweBooking\Component\Form;

use CMB2_Types;

class Custom_Fields {
	/**
	 * The fields template.
	 *
	 * @var string|null
	 */
	protected $template_path;

	/**
	 * Init the fields.
	 *
	 * @param string|null $template_path The template path.
	 */
	public function __construct( $template_path = null ) {
		$this->template_path = $template_path;
	}

	/**
	 * Helper to clone a field_types based on a field.
	 *
	 * @param  \CMB2_Field $field The field object.
	 * @param  array       $args  The clone field args.
	 * @return \CMB2_Types
	 */
	public function copy( $field, $args = [] ) {
		$clone = $field->get_field_clone( $args );

		// Set field the value.
		if ( isset( $args['value'] ) ) {
			$clone->value = $args['value'];
			$clone->escaped_value = null;
		}

		// Overwrite the _name.
		if ( isset( $args['_name'] ) ) {
			$clone->set_prop( '_name', $args['_name'] );
		}

		return new CMB2_Types( $clone );
	}

	/**
	 * Add new field type to the CMB2.
	 *
	 * @param string   $type          The field type.
	 * @param callable $sanitize_cb   Optional, the sanitize callback.
	 * @param boolean  $esc_recursive Is current field need recursive escape?.
	 */
	public function register( $type, $sanitize_cb = null, $esc_recursive = false ) {
		add_action( "cmb2_render_{$type}", $this->get_render_callback( $type ), 10, 5 );

		// Add fillter to sanitize if provided.
		if ( ! is_null( $sanitize_cb ) ) {
			add_filter( "cmb2_sanitize_{$type}", $this->get_sanitize_callback( $sanitize_cb ), 10, 5 );
		}

		// Need recursive escape?
		if ( $esc_recursive ) {
			add_filter( "cmb2_types_esc_{$type}", $this->get_recursive_escape_callback(), 10, 4 );
		}
	}

	/**
	 * Get the render field callback.
	 *
	 * @param  string $_type The field type name.
	 * @return \Closure
	 */
	protected function get_render_callback( $_type ) {
		$_template_path = $this->template_path ?: trailingslashit( __DIR__ ) . 'fields/';

		/**
		 * Rendering the field.
		 *
		 * @param array      $field         The passed in `CMB2_Field` object.
		 * @param mixed      $escaped_value The value of this field escaped.
		 * @param int        $object_id     The ID of the current object.
		 * @param string     $object_type   The type of object you are working with.
		 * @param CMB2_Types $types         The `CMB2_Types` object.
		 */
		return function( $field, $escaped_value, $object_id, $object_type, $types ) use ( $_type, $_template_path ) {
			include trailingslashit( $_template_path ) . "{$_type}.php";
		};
	}

	/**
	 * Get the sanitize callback.
	 *
	 * @param  callable $sanitize_cb The sanitize callback.
	 * @return \Closure
	 */
	public function get_sanitize_callback( $sanitize_cb ) {
		/**
		 * Filter the value before it is saved.
		 *
		 * @param bool|mixed     $check      The check variable.
		 * @param mixed          $value      The value to be saved to this field.
		 * @param int            $object_id  The ID of the object where the value will be saved.
		 * @param array          $field_args The current field's arguments.
		 * @param \CMB2_Sanitize $sanitizer  The `CMB2_Sanitize` object.
		 *
		 * @return mixed
		 */
		return function( $check, $value, $object_id, $field_args, $sanitizer ) use ( $sanitize_cb ) {
			return $sanitize_cb( $value );
		};
	}

	/**
	 * Get the recursive escape callback.
	 *
	 * @param  string $escape_cb The escapce callback, default: 'esc_attr'.
	 * @return \Closure
	 */
	protected function get_recursive_escape_callback( $escape_cb = 'esc_attr' ) {
		/**
		 * Escape recursive the field value.
		 *
		 * @param  mixed       $check      The check variable.
		 * @param  mixed       $meta_value The meta_value.
		 * @param  array       $field_args The current field's arguments.
		 * @param  \CMB2_Field $field      The `CMB2_Field` object.
		 *
		 * @return mixed
		 */
		return function( $check, $meta_value, $field_args, $field ) use ( $escape_cb ) {
			return abrs_recursive_sanitizer( $field->val_or_default( $meta_value ), $escape_cb );
		};
	}
}
