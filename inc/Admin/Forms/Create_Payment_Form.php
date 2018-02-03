<?php
namespace AweBooking\Admin\Forms;

use AweBooking\Dropdown;

class Create_Payment_Form extends Form_Abstract {
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
			'id'          => 'method',
			'type'        => 'select',
			'name'        => esc_html__( 'Payment method', 'awebooking' ),
			'options_cb'  => Dropdown::cb( 'get_payment_methods' ),
			'default'     => 'cash',
		]);

		$this->add_field([
			'id'          => 'amount',
			'type'        => 'text_small',
			'name'        => esc_html__( 'Amount', 'awebooking' ),
			'append'      => awebooking( 'currency' )->get_symbol(),
			'validate'    => 'required|numeric|min:0',
		]);

		$this->add_field([
			'id'          => 'comment',
			'type'        => 'textarea',
			'name'        => esc_html__( 'Payment Comment', 'awebooking' ),
		]);
	}
}
