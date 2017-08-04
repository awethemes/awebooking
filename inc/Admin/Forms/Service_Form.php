<?php
namespace AweBooking\Admin\Forms;

class Service_Form extends Form_Abstract {
	/**
	 * Form ID.
	 *
	 * @var string
	 */
	protected $form_id = '_extra_services';

	/**
	 * Register fields in to the CMB2.
	 *
	 * @return void
	 */
	protected function register_fields() {
		$this->add_field([
			'id'               => 'extra_services',
			'type'             => 'multicheck',
			'name'             => esc_html__( 'Extra services', 'awebooking' ),
			'default'          => 1,
			'select_all_button' => false,
			// 'validate'         => 'required|numeric|min:1',
			// 'sanitization_cb'  => 'absint',
		]);
	}
}
