<?php
namespace AweBooking\Admin\Forms;

class New_Source_Form extends Form_Abstract {
	/**
	 * The form ID.
	 *
	 * @var string
	 */
	protected $form_id = 'awebooking_new_reservation_source';

	/**
	 * {@inheritdoc}
	 */
	protected function fields() {
		$this->add_field([
			'id'          => 'new_source_name',
			'type'        => 'text_medium',
			'name'        => esc_html__( 'Source name', 'awebooking' ),
			'validate'    => 'required',
		]);
	}
}
