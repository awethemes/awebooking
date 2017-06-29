<?php
namespace Skeleton\CMB2\Render;

use CMB2_Field;

interface Render_Interface {
	/**
	 * Display CMB2 fields.
	 */
	public function display();

	/**
	 * Render a repeatable group.
	 *
	 * @see CMB2::render_group()
	 *
	 * @param  array $args     Array of field arguments for a group field parent.
	 * @return CMB2_Field|null Group field object.
	 */
	public function render_group( array $args );

	/**
	 * Manually render field.
	 *
	 * @param array      $field_args Array of field arguments.
	 * @param CMB2_Field $field      The CMB2_Field instance.
	 */
	public function render_field( array $field_args, CMB2_Field $field );
}
