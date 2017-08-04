<?php
namespace AweBooking\Admin\Forms;

use CMB2_hookup;
use Skeleton\CMB2\CMB2;

abstract class Form_Abstract extends CMB2 {
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
			'id'           => $this->form_id,
			'hookup'       => false,
			'cmb_styles'   => false,
			'show_on'      => [ 'options-page' => $this->form_id ],
			'object_types' => 'options-page',
		]);

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
	 * Returns field value by field ID.
	 *
	 * @param  string $field_id String field ID.
	 * @return mixed|null
	 */
	public function get_value( $field_id ) {
		$field = $this->get_field( $field_id );

		if ( false === $field ) {
			return;
		}

		return $field->val_or_default( $field->value() );
	}
}
