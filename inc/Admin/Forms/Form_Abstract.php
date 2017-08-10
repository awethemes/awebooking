<?php
namespace AweBooking\Admin\Forms;

use CMB2_hookup;
use Skeleton\CMB2\CMB2;

abstract class Form_Abstract extends CMB2 implements \ArrayAccess {
	/**
	 * Form ID.
	 *
	 * @var string
	 */
	protected $form_id;

	/**
	 * Form constructor.
	 */
	public function __construct() {
		parent::__construct([
			'id'         => $this->form_id,
			'hookup'     => false,
			'cmb_styles' => false,
		]);

		// Don't change this!
		// We need prevent CMB2 get field data from database.
		$this->object_id( '_' );
		$this->object_type( 'options-page' );

		$this->register_fields();
	}

	/**
	 * Register fields in to the CMB2.
	 *
	 * @return void
	 */
	abstract protected function register_fields();

	/**
	 * Output the form, alias of CMB2::show_form().
	 *
	 * @return void
	 */
	public function output() {
		$this->show_form();
	}

	/**
	 * Enqueue CMB2 and our styles, scripts.
	 *
	 * @access private
	 */
	public function enqueue_scripts() {
		CMB2_hookup::enqueue_cmb_js();
		CMB2_hookup::enqueue_cmb_css();
	}

	/**
	 * Get a field object.
	 *
	 * @param  mixed           $field The field id or field config array or CMB2_Field object.
	 * @param  CMB2_Field|null $group Optional, CMB2_Field object (group parent).
	 * @return Field_Proxy|null
	 */
	public function get_field( $field, $group = null ) {
		$field = parent::get_field( $field, $group );

		return $field ? new Field_Proxy( $this, $field ) : null;
	}

	/**
	 * Determine if an field exists.
	 *
	 * @param  mixed $key The field key ID.
	 * @return bool
	 */
	public function offsetExists( $key ) {
		return array_key_exists( $key, $this->prop( 'fields' ) );
	}

	/**
	 * Get an field at a given offset.
	 *
	 * @param  mixed $key The field key ID.
	 * @return mixed
	 */
	public function offsetGet( $key ) {
		return $this->get_field( $key );
	}

	/**
	 * Set the item at a given offset.
	 *
	 * @param  mixed $key  Field ID.
	 * @param  mixed $args Field args.
	 * @return void
	 */
	public function offsetSet( $key, $args ) {
		$this->add_field(
			array_merge( (array) $args, [ 'id' => $key ] )
		);
	}

	/**
	 * Unset the item at a given offset.
	 *
	 * @param  string $key Field key ID.
	 * @return void
	 */
	public function offsetUnset( $key ) {
		$this->remove_field( $key );
	}
}
